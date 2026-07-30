<?php

namespace App\Console\Commands;

use App\Models\EventGuest;
use App\Models\GuestFabricSelection;
use App\Models\GuestPackageSelection;
use App\Models\Guest;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * DeleteDuplicateGuests
 *
 * Runs the same duplicate-detection logic as guests:find-duplicates, then
 * permanently deletes every marked record inside a single database transaction.
 *
 * Tables potentially affected:
 *   - event_guest          (pivot / attendance rows)
 *   - guests               (guest profiles)
 *   - guest_fabric_selections
 *   - guest_package_selections
 */
class DeleteDuplicateGuests extends Command
{
    protected $signature   = 'guests:delete-duplicates
                                {--event= : Limit deletion to a specific event_id}
                                {--force : Skip confirmation prompt}';

    protected $description = 'Delete duplicate invited guests identified by guests:find-duplicates.';

    private const NAME_SIMILARITY_THRESHOLD = 85;

    public function handle(): int
    {
        $eventId = $this->option('event');

        $this->info('Loading invited event_guest records...');

        $query = EventGuest::with('guest')
            ->where('attendance_status', 'invited');

        if ($eventId) {
            $query->where('event_id', $eventId);
        }

        $invitedRows = $query->orderBy('created_at', 'desc')->orderBy('id', 'desc')->get();

        $this->info("Found {$invitedRows->count()} invited event_guest rows.");

        [$phoneGroups, $nameGroups] = $this->detectDuplicates($invitedRows);
        $allGroups = collect($phoneGroups)->merge($nameGroups);

        if ($allGroups->isEmpty()) {
            $this->info('No duplicate invited guests found. Nothing to delete.');
            return self::SUCCESS;
        }

        // Collect all IDs to delete.
        $eventGuestIdsToDelete      = [];
        $guestIdsToDelete           = [];
        $fabricSelectionIdsToDelete = [];
        $packageSelectionIdsToDelete = [];

        foreach ($allGroups as $group) {
            foreach ($group['mark'] as $eg) {
                $eventGuestIdsToDelete[] = $eg->id;
                $guestIdsToDelete[]      = $eg->guest_id;

                foreach (GuestFabricSelection::where('guest_id', $eg->guest_id)->get() as $r) {
                    $fabricSelectionIdsToDelete[] = $r->id;
                }
                foreach (GuestPackageSelection::where('guest_id', $eg->guest_id)->get() as $r) {
                    $packageSelectionIdsToDelete[] = $r->id;
                }
            }
        }

        $eventGuestIdsToDelete       = array_unique($eventGuestIdsToDelete);
        $guestIdsToDelete            = array_unique($guestIdsToDelete);
        $fabricSelectionIdsToDelete  = array_unique($fabricSelectionIdsToDelete);
        $packageSelectionIdsToDelete = array_unique($packageSelectionIdsToDelete);

        // Summary before confirmation.
        $this->table(
            ['Table', 'Rows to delete'],
            [
                ['event_guest',               count($eventGuestIdsToDelete)],
                ['guests',                    count($guestIdsToDelete)],
                ['guest_fabric_selections',   count($fabricSelectionIdsToDelete)],
                ['guest_package_selections',  count($packageSelectionIdsToDelete)],
            ]
        );

        if (!$this->option('force') && !$this->confirm('Proceed with permanent deletion?', false)) {
            $this->warn('Aborted. No data was deleted.');
            return self::SUCCESS;
        }

        // Execute inside a transaction so it is fully atomic.
        DB::transaction(function () use (
            $eventGuestIdsToDelete,
            $guestIdsToDelete,
            $fabricSelectionIdsToDelete,
            $packageSelectionIdsToDelete
        ) {
            // Delete child records first to avoid FK constraint failures.
            if (!empty($fabricSelectionIdsToDelete)) {
                GuestFabricSelection::whereIn('id', $fabricSelectionIdsToDelete)->delete();
            }

            if (!empty($packageSelectionIdsToDelete)) {
                GuestPackageSelection::whereIn('id', $packageSelectionIdsToDelete)->delete();
            }

            // Delete event_guest rows.
            EventGuest::whereIn('id', $eventGuestIdsToDelete)->delete();

            // Delete guest profiles (Guest model does not use SoftDeletes trait).
            Guest::whereIn('id', $guestIdsToDelete)->delete();
        });

        $this->newLine();
        $this->info('Deletion complete. Tables affected:');
        $this->newLine();

        $affected = [];

        if (!empty($eventGuestIdsToDelete)) {
            $affected[] = ['event_guest', count($eventGuestIdsToDelete), 'Duplicate invited pivot rows'];
        }
        if (!empty($guestIdsToDelete)) {
            $affected[] = ['guests', count($guestIdsToDelete), 'Duplicate guest profiles'];
        }
        if (!empty($fabricSelectionIdsToDelete)) {
            $affected[] = ['guest_fabric_selections', count($fabricSelectionIdsToDelete), 'Fabric selections linked to deleted guests'];
        }
        if (!empty($packageSelectionIdsToDelete)) {
            $affected[] = ['guest_package_selections', count($packageSelectionIdsToDelete), 'Package selections linked to deleted guests'];
        }

        $this->table(['Table', 'Rows deleted', 'Reason'], $affected);

        return self::SUCCESS;
    }

    // ------------------------------------------------------------------
    // Duplicate detection (identical logic to FindDuplicateGuests)
    // ------------------------------------------------------------------

    private function detectDuplicates(Collection $invitedRows): array
    {
        $phoneGroups  = [];
        $nameGroups   = [];
        $processedIds = [];

        $byPhone = $invitedRows
            ->filter(fn(EventGuest $eg) => !empty($eg->guest?->phone))
            ->groupBy(fn(EventGuest $eg) => $this->normalisePhone($eg->guest->phone));

        foreach ($byPhone as $phone => $rows) {
            if ($rows->count() < 2) {
                continue;
            }

            $sorted = $rows->sortByDesc(fn(EventGuest $eg) => [$eg->created_at?->timestamp ?? 0, $eg->id]);
            $keep   = $sorted->first();
            $mark   = $sorted->slice(1)->values();

            $phoneGroups[] = ['method' => 'Phone', 'keep' => $keep, 'mark' => $mark];

            foreach ($rows as $eg) {
                $processedIds[] = $eg->id;
            }
        }

        $unmatched    = $invitedRows->reject(fn(EventGuest $eg) => in_array($eg->id, $processedIds, true));
        $unmatchedArr = $unmatched->values()->all();
        $nameDone     = [];

        for ($i = 0; $i < count($unmatchedArr); $i++) {
            if (in_array($i, $nameDone, true)) {
                continue;
            }

            $egA   = $unmatchedArr[$i];
            $nameA = $this->normaliseName($egA->guest?->first_name . ' ' . $egA->guest?->last_name);
            $group = collect([$egA]);

            for ($j = $i + 1; $j < count($unmatchedArr); $j++) {
                if (in_array($j, $nameDone, true)) {
                    continue;
                }

                $egB   = $unmatchedArr[$j];
                $nameB = $this->normaliseName($egB->guest?->first_name . ' ' . $egB->guest?->last_name);

                if ($this->nameSimilarity($nameA, $nameB) >= self::NAME_SIMILARITY_THRESHOLD) {
                    $group->push($egB);
                    $nameDone[] = $j;
                }
            }

            if ($group->count() >= 2) {
                $nameDone[] = $i;
                $sorted     = $group->sortByDesc(fn(EventGuest $eg) => [$eg->created_at?->timestamp ?? 0, $eg->id]);

                $nameGroups[] = [
                    'method' => 'Name',
                    'keep'   => $sorted->first(),
                    'mark'   => $sorted->slice(1)->values(),
                ];
            }
        }

        return [$phoneGroups, $nameGroups];
    }

    private function normalisePhone(string $phone): string
    {
        return preg_replace('/\D/', '', $phone);
    }

    private function normaliseName(string $name): string
    {
        $name = strtolower($name);
        $name = preg_replace('/[^a-z\s]/', '', $name);
        $name = preg_replace('/\b[a-z]\b/', '', $name);
        $name = preg_replace('/\s+/', ' ', trim($name));

        return $name;
    }

    private function nameSimilarity(string $a, string $b): int
    {
        if ($a === '' || $b === '') {
            return 0;
        }

        similar_text($a, $b, $percent);

        return (int) round($percent);
    }
}
