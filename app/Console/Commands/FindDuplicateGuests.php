<?php

namespace App\Console\Commands;

use App\Models\EventGuest;
use App\Models\Guest;
use App\Models\GuestFabricSelection;
use App\Models\GuestPackageSelection;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * FindDuplicateGuests
 *
 * Identifies duplicate "invited" guests across the event_guest and guests tables.
 * This command is READ-ONLY — it does NOT delete, soft-delete, or modify any data.
 *
 * Duplicate detection rules:
 *  1. Phone match  — two or more invited event_guest rows share the same non-null phone number.
 *  2. Name similarity — conservative fuzzy name match (similar_text >= 85%) used only when
 *     phone numbers are absent or differ.
 *
 * For each duplicate group the NEWEST record (by created_at DESC, then id DESC) is kept;
 * all older records are flagged for deletion.
 *
 * Output: a structured review report written to storage/app/duplicate_guests_report.txt
 *         and echoed to the console.
 */
class FindDuplicateGuests extends Command
{
    protected $signature   = 'guests:find-duplicates
                                {--event= : Limit scan to a specific event_id}
                                {--output= : Path to write the report (default: storage/app/duplicate_guests_report.txt)}';

    protected $description = 'Identify duplicate invited guests and produce a review report. No data is deleted.';

    // Minimum name-similarity percentage to consider two names a potential duplicate.
    private const NAME_SIMILARITY_THRESHOLD = 85;

    public function handle(): int
    {
        $eventId    = $this->option('event');
        $outputPath = $this->option('output') ?? storage_path('app/duplicate_guests_report.txt');

        $this->info('Loading invited event_guest records…');

        // ------------------------------------------------------------------
        // 1. Load all "invited" event_guest rows with their guest data.
        //    Scope to a single event if --event was provided.
        // ------------------------------------------------------------------
        $query = EventGuest::with('guest')
            ->where('attendance_status', 'invited');

        if ($eventId) {
            $query->where('event_id', $eventId);
        }

        /** @var Collection<int, EventGuest> $invitedRows */
        $invitedRows = $query->orderBy('created_at', 'desc')->orderBy('id', 'desc')->get();

        $this->info("Found {$invitedRows->count()} invited event_guest rows.");

        // ------------------------------------------------------------------
        // 2. Detect duplicates.
        // ------------------------------------------------------------------
        [$phoneGroups, $nameGroups, $processedGuestIds] = $this->detectDuplicates($invitedRows);

        $allGroups = collect($phoneGroups)->merge($nameGroups);

        if ($allGroups->isEmpty()) {
            $this->info('No duplicate invited guests found.');
            return self::SUCCESS;
        }

        $this->info('Duplicate groups found: ' . $allGroups->count());

        // ------------------------------------------------------------------
        // 3. Build the report.
        // ------------------------------------------------------------------
        $report = $this->buildReport($allGroups);

        // Write to file.
        file_put_contents($outputPath, $report);

        $this->line($report);
        $this->newLine();
        $this->info("Report written to: {$outputPath}");
        $this->warn('NO DATA HAS BEEN MODIFIED. This is a review-only report.');

        return self::SUCCESS;
    }

    // ------------------------------------------------------------------
    // Duplicate detection
    // ------------------------------------------------------------------

    /**
     * @return array{0: array, 1: array, 2: array}
     */
    private function detectDuplicates(Collection $invitedRows): array
    {
        $phoneGroups     = [];
        $nameGroups      = [];
        $processedIds    = [];   // event_guest IDs already assigned to a phone group

        // ---- Rule 1: Phone match ----------------------------------------
        // Group rows by normalised phone number (non-null only).
        $byPhone = $invitedRows
            ->filter(fn(EventGuest $eg) => !empty($eg->guest?->phone))
            ->groupBy(fn(EventGuest $eg) => $this->normalisePhone($eg->guest->phone));

        foreach ($byPhone as $phone => $rows) {
            if ($rows->count() < 2) {
                continue;
            }

            // Sort: newest first (already ordered from query, but re-sort per group).
            $sorted = $rows->sortByDesc(fn(EventGuest $eg) => [$eg->created_at?->timestamp ?? 0, $eg->id]);
            $keep   = $sorted->first();
            $mark   = $sorted->slice(1)->values();

            $phoneGroups[] = [
                'method'     => 'Phone',
                'phone'      => $phone,
                'similarity' => null,
                'keep'       => $keep,
                'mark'       => $mark,
            ];

            foreach ($rows as $eg) {
                $processedIds[] = $eg->id;
            }
        }

        // ---- Rule 2: Name similarity (only for rows not already matched) --
        $unmatched = $invitedRows->reject(fn(EventGuest $eg) => in_array($eg->id, $processedIds, true));

        // Convert to array for O(n²) comparison.
        $unmatchedArr  = $unmatched->values()->all();
        $nameProcessed = [];

        for ($i = 0; $i < count($unmatchedArr); $i++) {
            if (in_array($i, $nameProcessed, true)) {
                continue;
            }

            $egA    = $unmatchedArr[$i];
            $nameA  = $this->normaliseName($egA->guest?->first_name . ' ' . $egA->guest?->last_name);
            $group  = collect([$egA]);

            for ($j = $i + 1; $j < count($unmatchedArr); $j++) {
                if (in_array($j, $nameProcessed, true)) {
                    continue;
                }

                $egB   = $unmatchedArr[$j];
                $nameB = $this->normaliseName($egB->guest?->first_name . ' ' . $egB->guest?->last_name);

                $similarity = $this->nameSimilarity($nameA, $nameB);

                if ($similarity >= self::NAME_SIMILARITY_THRESHOLD) {
                    $group->push($egB);
                    $nameProcessed[] = $j;
                }
            }

            if ($group->count() >= 2) {
                $nameProcessed[] = $i;

                $sorted = $group->sortByDesc(fn(EventGuest $eg) => [$eg->created_at?->timestamp ?? 0, $eg->id]);
                $keep   = $sorted->first();
                $mark   = $sorted->slice(1)->values();

                // Compute representative similarity (keep vs first mark).
                $simA = $this->normaliseName($keep->guest?->first_name . ' ' . $keep->guest?->last_name);
                $simB = $this->normaliseName($mark->first()->guest?->first_name . ' ' . $mark->first()->guest?->last_name);

                $nameGroups[] = [
                    'method'     => 'Name',
                    'phone'      => null,
                    'similarity' => $this->nameSimilarity($simA, $simB),
                    'keep'       => $keep,
                    'mark'       => $mark,
                ];
            }
        }

        return [$phoneGroups, $nameGroups, $processedIds];
    }

    // ------------------------------------------------------------------
    // Related-record lookup
    // ------------------------------------------------------------------

    private function relatedRecords(EventGuest $eg): array
    {
        $guestId = $eg->guest_id;
        $related = [];

        // GuestFabricSelection
        $fabricSelections = GuestFabricSelection::where('guest_id', $guestId)->get();
        foreach ($fabricSelections as $rec) {
            $related[] = "GuestFabricSelection #{$rec->id} (event_id={$rec->event_id}, payment_status={$rec->payment_status})";
        }

        // GuestPackageSelection
        $packageSelections = GuestPackageSelection::where('guest_id', $guestId)->get();
        foreach ($packageSelections as $rec) {
            $related[] = "GuestPackageSelection #{$rec->id} (event_id={$rec->event_id}, payment_status={$rec->payment_status})";
        }

        return $related;
    }

    // ------------------------------------------------------------------
    // Report builder
    // ------------------------------------------------------------------

    private function buildReport(Collection $allGroups): string
    {
        $lines   = [];
        $lines[] = str_repeat('=', 70);
        $lines[] = 'DUPLICATE GUEST REVIEW REPORT';
        $lines[] = 'Generated: ' . now()->toDateTimeString();
        $lines[] = 'IMPORTANT: This is a READ-ONLY report. No data has been modified.';
        $lines[] = str_repeat('=', 70);
        $lines[] = '';

        $groupNum = 1;

        foreach ($allGroups as $group) {
            /** @var EventGuest $keep */
            $keep = $group['keep'];

            /** @var Collection $markList */
            $markList = $group['mark'];

            $lines[] = "Duplicate Group #{$groupNum}";
            $lines[] = str_repeat('-', 40);

            $lines[] = 'Matched By: ' . $group['method'];

            if ($group['method'] === 'Phone') {
                $lines[] = 'Phone: ' . $group['phone'];
            } else {
                $lines[] = 'Similarity: ' . $group['similarity'] . '%';
            }

            $lines[] = '';

            // KEEP
            $lines[] = '[KEEP]:';
            $lines[] = $this->formatEventGuest($keep);
            $lines[] = '';

            // MARK
            foreach ($markList as $eg) {
                $lines[] = '[MARK FOR DELETION]:';
                $lines[] = $this->formatEventGuest($eg);

                $related = $this->relatedRecords($eg);
                if (!empty($related)) {
                    $lines[] = '   Related Records:';
                    foreach ($related as $rec) {
                        $lines[] = "     - {$rec}";
                    }
                } else {
                    $lines[] = '   Related Records: none';
                }

                $lines[] = '   Reason: Older invited duplicate';
                $lines[] = '';
            }

            $lines[] = str_repeat('-', 70);
            $lines[] = '';

            $groupNum++;
        }

        $totalMark = $allGroups->sum(fn($g) => $g['mark']->count());
        $lines[] = "SUMMARY";
        $lines[] = "Duplicate groups : " . $allGroups->count();
        $lines[] = "Records to keep  : " . $allGroups->count();
        $lines[] = "Records to mark  : {$totalMark}";
        $lines[] = '';
        $lines[] = 'NO DATA HAS BEEN MODIFIED.';

        return implode(PHP_EOL, $lines);
    }

    private function formatEventGuest(EventGuest $eg): string
    {
        $guest   = $eg->guest;
        $name    = $guest ? trim("{$guest->title} {$guest->first_name} {$guest->last_name}") : 'N/A';
        $phone   = $guest?->phone ?? 'N/A';
        $created = $eg->created_at?->toDateString() ?? 'N/A';

        return "   Guest ID: {$eg->guest_id} | EventGuest ID: {$eg->id} | Name: {$name} | Phone: {$phone} | Created: {$created}";
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function normalisePhone(string $phone): string
    {
        // Strip all non-digit characters for comparison.
        return preg_replace('/\D/', '', $phone);
    }

    private function normaliseName(string $name): string
    {
        // Lowercase, remove punctuation, collapse spaces, strip middle initials.
        $name = strtolower($name);
        $name = preg_replace('/[^a-z\s]/', '', $name);
        $name = preg_replace('/\b[a-z]\b/', '', $name);  // remove single-letter tokens (middle initials)
        $name = preg_replace('/\s+/', ' ', trim($name));

        return $name;
    }

    /**
     * Returns a similarity percentage (0–100) between two normalised names.
     * Uses PHP's similar_text() which works well for conservative matching.
     */
    private function nameSimilarity(string $a, string $b): int
    {
        if ($a === '' || $b === '') {
            return 0;
        }

        similar_text($a, $b, $percent);

        return (int) round($percent);
    }
}