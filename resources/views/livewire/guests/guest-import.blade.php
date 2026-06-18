<div>
    @if (session()->has('message'))
        <div
            class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-300">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"></path>
                </svg>
                {{ session('message') }}
            </div>
        </div>
    @endif

    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Import Guests</h1>
                <p class="text-gray-600 dark:text-gray-400">Upload a CSV file to import multiple guests at once</p>
            </div>
            <a href="{{ route('guests.list') }}" wire:navigate
                class="inline-flex items-center px-4 py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-xl transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Go Back
            </a>
        </div>

        <!-- Main Content Card -->
        <div
            class="relative h-full flex-1 overflow-hidden rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-800 shadow-sm">
            <div class="p-8">
                <!-- Import Form -->
                <form wire:submit.prevent="handleImportAction">
                    <div class="space-y-6">
                        @if ($showNotificationConfirm)
                            <div
                                class="rounded-xl border border-amber-300 bg-amber-50 p-5 dark:border-amber-700 dark:bg-amber-950/40">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5">
                                        <flux:icon.exclamation-triangle
                                            class="h-6 w-6 text-amber-600 dark:text-amber-400" />
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-base font-semibold text-amber-900 dark:text-amber-200">
                                            Send guest notifications?
                                        </h3>
                                        <p class="mt-1 text-sm text-amber-800 dark:text-amber-300">
                                            Do you want to send email and SMS to imported guests after saving them?
                                        </p>

                                        <div class="mt-4 flex flex-wrap gap-3">
                                            <button type="button" wire:click="confirmImportWithNotifications"
                                                wire:loading.attr="disabled"
                                                wire:target="confirmImportWithNotifications"
                                                class="inline-flex items-center rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                                                <span wire:loading.remove
                                                    wire:target="confirmImportWithNotifications">Yes! Send email and
                                                    SMS</span>
                                                <span wire:loading wire:target="confirmImportWithNotifications"
                                                    class="inline-flex items-center gap-2">
                                                    <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                                        fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                            stroke="currentColor" stroke-width="4">
                                                        </circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                                    </svg>
                                                    Importing...
                                                </span>
                                            </button>
                                            <button type="button" wire:click="confirmImportWithoutNotifications"
                                                wire:loading.attr="disabled"
                                                wire:target="confirmImportWithoutNotifications"
                                                class="inline-flex items-center rounded-lg bg-gray-700 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 dark:bg-zinc-600 dark:hover:bg-zinc-500">
                                                <span wire:loading.remove
                                                    wire:target="confirmImportWithoutNotifications">No! Do not
                                                    send</span>
                                                <span wire:loading wire:target="confirmImportWithoutNotifications"
                                                    class="inline-flex items-center gap-2">
                                                    <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                                        fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                            stroke="currentColor" stroke-width="4">
                                                        </circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                                    </svg>
                                                    Importing...
                                                </span>
                                            </button>
                                            <button type="button" wire:click="cancelNotificationPrompt"
                                                class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-zinc-700">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Event Selection (Optional) -->
                        @if ($events->count() > 0)
                            <div>
                                <label for="event_id"
                                    class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                    Select Event (Optional)
                                </label>
                                <select wire:model="event_id" id="event_id"
                                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-200">
                                    <option value="">No event (import guests only)</option>
                                    @foreach ($events as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('event_id')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <!-- File Upload -->
                        <div>
                            <label for="file"
                                class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                Upload CSV File <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="file" wire:model="file" id="file" accept=".csv,.txt"
                                    class="block w-full text-sm text-gray-900 dark:text-gray-300 file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-pink-50 dark:file:bg-pink-900/30 file:text-pink-700 dark:file:text-pink-300 hover:file:bg-pink-100 dark:hover:file:bg-pink-900/50 file:cursor-pointer cursor-pointer bg-gray-50 dark:bg-zinc-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-200" />
                            </div>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                Accepted formats: Excel(CSV), TXT. Maximum file size: 10MB
                            </p>
                            @error('file')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Column Mapping -->
                        {{-- @if ($showPreview && !empty($csvColumns))
                            <div class="border-t border-gray-200 dark:border-zinc-700 pt-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Column Mapping</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    Map your CSV columns to the guest fields. Required fields are marked with <span
                                        class="text-red-500">*</span>
                                </p>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                            Title
                                        </label>
                                        <select wire:model="columnMap.title"
                                            class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                            <option value="">Select column...</option>
                                            @foreach ($csvColumns as $column)
                                                <option value="{{ $column }}">{{ $column }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                            First Name <span class="text-red-500">*</span>
                                        </label>
                                        <select wire:model="columnMap.first_name" required
                                            class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                            <option value="">Select column...</option>
                                            @foreach ($csvColumns as $column)
                                                <option value="{{ $column }}"
                                                    {{ $columnMap['first_name'] == $column ? 'selected' : '' }}>
                                                    {{ $column }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                            Last Name <span class="text-red-500">*</span>
                                        </label>
                                        <select wire:model="columnMap.last_name" required
                                            class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                            <option value="">Select column...</option>
                                            @foreach ($csvColumns as $column)
                                                <option value="{{ $column }}"
                                                    {{ $columnMap['last_name'] == $column ? 'selected' : '' }}>
                                                    {{ $column }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                            Email
                                        </label>
                                        <select wire:model="columnMap.email"
                                            class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                            <option value="">Select column...</option>
                                            @foreach ($csvColumns as $column)
                                                <option value="{{ $column }}"
                                                    {{ $columnMap['email'] == $column ? 'selected' : '' }}>
                                                    {{ $column }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                            Phone
                                        </label>
                                        <select wire:model="columnMap.phone"
                                            class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                            <option value="">Select column...</option>
                                            @foreach ($csvColumns as $column)
                                                <option value="{{ $column }}"
                                                    {{ $columnMap['phone'] == $column ? 'selected' : '' }}>
                                                    {{ $column }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif --}}

                        <!-- Preview Table -->
                        @if ($showPreview && !empty($importPreview))
                            <div class="border-t border-gray-200 dark:border-zinc-700 pt-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Preview (First 5
                                    Rows)</h3>
                                <div class="overflow-x-auto">
                                    <table
                                        class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-lg">
                                        <thead class="bg-gray-50 dark:bg-zinc-700">
                                            <tr>
                                                @foreach ($csvColumns as $column)
                                                    <th
                                                        class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-600">
                                                        {{ $column }}
                                                    </th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                            @foreach ($importPreview as $row)
                                                <tr class="bg-white dark:bg-zinc-800">
                                                    @foreach ($csvColumns as $column)
                                                        <td class="px-4 py-3 text-gray-900 dark:text-white">
                                                            {{ $row[$column] ?? '' }}
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <!-- Import Results -->
                        @if (!empty($importResults))
                            <div class="border-t border-gray-200 dark:border-zinc-700 pt-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Import Results
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                                    <div
                                        class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl border border-blue-200 dark:border-blue-800">
                                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                            {{ $importResults['total'] }}</div>
                                        <div class="text-sm text-blue-600 dark:text-blue-400">Total Rows</div>
                                    </div>
                                    <div
                                        class="bg-emerald-50 dark:bg-emerald-900/20 p-4 rounded-xl border border-emerald-200 dark:border-emerald-800">
                                        <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                                            {{ $importResults['successful'] }}</div>
                                        <div class="text-sm text-emerald-600 dark:text-emerald-400">Successful</div>
                                    </div>
                                    <div
                                        class="bg-red-50 dark:bg-red-900/20 p-4 rounded-xl border border-red-200 dark:border-red-800">
                                        <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                                            {{ $importResults['failed'] }}</div>
                                        <div class="text-sm text-red-600 dark:text-red-400">Failed</div>
                                    </div>
                                    @if ($importResults['emails_sent'] > 0)
                                        <div
                                            class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-xl border border-purple-200 dark:border-purple-800">
                                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                                {{ $importResults['emails_sent'] }}</div>
                                            <div class="text-sm text-purple-600 dark:text-purple-400">Emails Sent</div>
                                        </div>
                                    @endif
                                </div>

                                @if (!empty($importResults['errors']))
                                    <div
                                        class="bg-red-50 dark:bg-red-900/20 p-4 rounded-xl border border-red-200 dark:border-red-800">
                                        <h4 class="font-semibold text-red-700 dark:text-red-300 mb-2">Import Errors:
                                        </h4>
                                        <ul class="space-y-1 text-sm text-red-600 dark:text-red-400">
                                            @foreach (array_slice($importResults['errors'], 0, 10) as $error)
                                                <li>Row {{ $error['row'] }}: {{ $error['error'] }}</li>
                                            @endforeach
                                        </ul>
                                        @if (count($importResults['errors']) > 10)
                                            <p class="text-sm text-red-600 dark:text-red-400 mt-2">
                                                ... and {{ count($importResults['errors']) - 10 }} more errors
                                            </p>
                                        @endif
                                    </div>
                                @endif

                                @if (!empty($importResults['email_errors']))
                                    <div
                                        class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-xl border border-orange-200 dark:border-orange-800 mt-4">
                                        <h4 class="font-semibold text-orange-700 dark:text-orange-300 mb-2">Email
                                            Errors:</h4>
                                        <ul class="space-y-1 text-sm text-orange-600 dark:text-orange-400">
                                            @foreach (array_slice($importResults['email_errors'], 0, 10) as $error)
                                                <li>{{ $error['guest'] }} ({{ $error['email'] }}):
                                                    {{ $error['error'] }}</li>
                                            @endforeach
                                        </ul>
                                        @if (count($importResults['email_errors']) > 10)
                                            <p class="text-sm text-orange-600 dark:text-orange-400 mt-2">
                                                ... and {{ count($importResults['email_errors']) - 10 }} more email
                                                errors
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-4 pt-6 border-t border-gray-200 dark:border-zinc-700">
                            <button type="button" wire:click="resetFile"
                                class="px-6 py-3 rounded-xl bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-zinc-600 transition-all duration-200 font-medium">
                                Reset
                            </button>

                            <button type="submit"
                                class="px-6 py-3 rounded-xl bg-gradient-to-r from-pink-600 to-pink-700 hover:from-pink-700 hover:to-pink-800 text-white font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled"
                                wire:target="handleImportAction,confirmImportWithNotifications,confirmImportWithoutNotifications">
                                <span wire:loading.remove
                                    wire:target="handleImportAction,confirmImportWithNotifications,confirmImportWithoutNotifications">
                                    Import Guests
                                </span>
                                <span wire:loading
                                    wire:target="handleImportAction,confirmImportWithNotifications,confirmImportWithoutNotifications">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Importing...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- CSV Format Help -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-300 mb-3">Guest List Excel Format
                    </h3>
                    <p class="text-blue-800 dark:text-blue-400">
                        Your CSV file should contain the following columns. The first row should be the header row:
                    </p>
                </div>
                <a href="{{ route('guests.import.template') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Download Template
                </a>
            </div>
            <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 font-mono text-sm overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-600">
                            <th class="text-left py-2 text-blue-600 dark:text-blue-400">Column</th>
                            <th class="text-left py-2 text-blue-600 dark:text-blue-400">Required</th>
                            <th class="text-left py-2 text-blue-600 dark:text-blue-400">Description</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-white">event</td>
                            <td class="py-2 text-gray-500 dark:text-gray-400">Optional</td>
                            <td class="py-2 text-gray-500 dark:text-gray-400">Event name to auto-match and attach each
                                guest</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-white">title</td>
                            <td class="py-2 text-red-500">Required</td>
                            <td class="py-2 text-gray-500 dark:text-gray-400">Mr, Mrs, Ms, Dr, etc.</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-white">first_name</td>
                            <td class="py-2 text-red-500">Required</td>
                            <td class="py-2 text-gray-500 dark:text-gray-400">Guest's first name</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-white">last_name</td>
                            <td class="py-2 text-red-500">Required</td>
                            <td class="py-2 text-gray-500 dark:text-gray-400">Guest's last name</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-white">email</td>
                            <td class="py-2 text-gray-500 dark:text-gray-400">Optional</td>
                            <td class="py-2 text-gray-500 dark:text-gray-400">Guest's email address</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-white">phone</td>
                            <td class="py-2 text-red-500">Required</td>
                            <td class="py-2 text-gray-500 dark:text-gray-400">Guest's phone number</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-white">address</td>
                            <td class="py-2 text-gray-500 dark:text-gray-400">Optional</td>
                            <td class="py-2 text-gray-500 dark:text-gray-400">Guest's full address</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-white">state</td>
                            <td class="py-2 text-gray-500 dark:text-gray-400">Optional</td>
                            <td class="py-2 text-gray-500 dark:text-gray-400">State/Province name (e.g., Lagos, Abuja)
                            </td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-white">city</td>
                            <td class="py-2 text-gray-500 dark:text-gray-400">Optional</td>
                            <td class="py-2 text-gray-500 dark:text-gray-400">City/Town name</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex items-center text-blue-700 dark:text-blue-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm">
                    Tip: You can either select one event above for the whole import, or include an <code>event</code>
                    column in your CSV to match guests to events by name.
                </span>
            </div>
        </div>
    </div>
</div>
