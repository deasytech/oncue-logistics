@php use Illuminate\Support\Str; @endphp
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

    @if (session()->has('error'))
        <div
            class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl dark:bg-red-900/20 dark:border-red-800 dark:text-red-300">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd"></path>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Payment Receipts</h1>
                <p class="text-gray-600 dark:text-gray-400">Upload and manage your payment receipts</p>
            </div>
        </div>

        <!-- Upload Form Card -->
        <div
            class="relative overflow-hidden rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-800 shadow-sm">
            <div class="p-8">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Upload New Receipt</h2>

                <form wire:submit.prevent="uploadReceipt" class="space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- File Upload -->
                        <div>
                            <label for="receiptFile"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Receipt File <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="file" wire:model="receiptFile" id="receiptFile"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    class="block w-full text-sm text-gray-900 dark:text-gray-300 file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 dark:file:bg-blue-900/30 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-blue-900/50 transition-all duration-200">
                            </div>
                            @error('receiptFile')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                Accepted formats: PDF, JPG, JPEG, PNG. Maximum file size: 5MB
                            </p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Description (Optional)
                            </label>
                            <textarea wire:model="description" id="description" rows="3"
                                placeholder="Add a brief description about this receipt..."
                                class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"></textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed"
                            wire:loading.attr="disabled">
                            <svg wire:loading.remove class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                                </path>
                            </svg>
                            <svg wire:loading class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span wire:loading.remove>Upload Receipt</span>
                            <span wire:loading>Uploading...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Receipts List Card -->
        <div
            class="relative h-full flex-1 overflow-hidden rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-800 shadow-sm">
            <div class="p-8">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Your Receipts</h2>

                @if ($receipts->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-zinc-700 dark:text-gray-300">
                                <tr>
                                    <th scope="col" class="px-6 py-4 font-semibold">File</th>
                                    <th scope="col" class="px-6 py-4 font-semibold">Description</th>
                                    <th scope="col" class="px-6 py-4 font-semibold">Size</th>
                                    <th scope="col" class="px-6 py-4 font-semibold">Status</th>
                                    <th scope="col" class="px-6 py-4 font-semibold">Uploaded</th>
                                    <th scope="col" class="px-6 py-4 font-semibold text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-zinc-700">
                                @foreach ($receipts as $receipt)
                                    <tr wire:key="receipt-{{ $receipt->id }}"
                                        class="bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700/50 transition-colors duration-200">
                                        <td class="px-6 py-6">
                                            <div class="flex items-center">
                                                <div
                                                    class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center mr-3">
                                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900 dark:text-white">
                                                        {{ $receipt->original_name }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $receipt->mime_type }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-6">
                                            <div class="text-gray-900 dark:text-white">
                                                {{ $receipt->description ?: 'No description' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-6">
                                            <div class="text-gray-900 dark:text-white font-medium">
                                                {{ $receipt->file_size_formatted }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-6">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $receipt->status_badge_class }} dark:text-white">
                                                {{ $receipt->status_text }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-6">
                                            <div class="text-gray-900 dark:text-white">
                                                {{ $receipt->created_at->format('M d, Y') }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $receipt->created_at->diffForHumans() }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-6">
                                            <div class="flex items-center justify-center space-x-3">
                                                <button wire:click="viewReceipt({{ $receipt->id }})"
                                                    class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-900/30 transition-all duration-200">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                                        </path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <button wire:click="confirmDelete({{ $receipt->id }})"
                                                    class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 transition-all duration-200">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($receipts->hasPages())
                        <div
                            class="mt-8 flex items-center justify-between border-t border-gray-100 dark:border-zinc-700 pt-6">
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-medium">{{ $receipts->total() }}</span> total receipts
                            </div>
                            <div class="flex items-center space-x-2">
                                {{ $receipts->links() }}
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-16">
                        <div class="relative">
                            <div
                                class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-zinc-700 dark:to-zinc-600 flex items-center justify-center">
                                <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                            No Receipts Uploaded
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">
                            Upload your first payment receipt using the form above. Your receipts will be reviewed by
                            our team.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-data="{ open: @entangle('confirmingDelete') }" x-show="open" x-transition.opacity.duration.300ms
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
        style="display: none;">
        <div x-transition.duration.300ms.scale.origin.center
            class="bg-white dark:bg-zinc-900 rounded-3xl shadow-2xl max-w-md w-full mx-4 p-8 text-center relative border border-gray-100 dark:border-zinc-800">
            <!-- Animated Warning Icon -->
            <div x-data="{ bounce: true }" x-init="setInterval(() => bounce = !bounce, 1200)"
                class="w-20 h-20 mx-auto mb-5 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center"
                :class="{ 'animate-bounce': bounce }">
                <svg class="w-10 h-10 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z" />
                </svg>
            </div>

            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">Delete Receipt?</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-8">
                Are you sure you want to permanently delete this receipt?
                <br>This action <span class="text-red-600 font-semibold">cannot be undone</span>.
            </p>
            <div class="flex justify-center gap-4">
                <button wire:click="cancelDelete"
                    class="px-6 py-2.5 rounded-xl bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-zinc-700 transition-all duration-200 font-medium">
                    Cancel
                </button>

                <button wire:click="deleteReceipt"
                    class="px-6 py-2.5 rounded-xl bg-red-600 text-white hover:bg-red-700 transition-all duration-200 font-semibold shadow-md hover:shadow-lg">
                    Yes, Delete
                </button>
            </div>
            <button wire:click="cancelDelete"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-all duration-150">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- View Receipt Lightbox Modal -->
    <div x-data="{ open: @entangle('showViewModal') }" x-show="open" x-transition.opacity.duration.300ms
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm"
        style="display: none;" @click="if ($event.target === $el) $wire.closeViewModal()">
        <div x-transition.duration.300ms.scale.origin.center
            class="relative bg-white dark:bg-zinc-900 rounded-3xl shadow-2xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden border border-gray-200 dark:border-zinc-700">

            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-zinc-700">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        {{ $viewingReceipt?->original_name }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Uploaded {{ $viewingReceipt?->created_at?->diffForHumans() }}
                    </p>
                </div>
                <button wire:click="closeViewModal"
                    class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-all duration-150 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-6 overflow-auto max-h-[calc(90vh-200px)]">
                @if ($viewingReceipt)
                    @if (Str::contains($viewingReceipt->mime_type, 'pdf'))
                        <!-- PDF Viewer -->
                        <div class="w-full h-[60vh] bg-gray-100 dark:bg-zinc-800 rounded-xl overflow-hidden">
                            <iframe src="{{ Storage::url($viewingReceipt->file_path) }}"
                                class="w-full h-full border-0" title="{{ $viewingReceipt->original_name }}">
                            </iframe>
                        </div>
                    @else
                        <!-- Image Viewer -->
                        <div class="flex items-center justify-center">
                            <img src="{{ Storage::url($viewingReceipt->file_path) }}"
                                alt="{{ $viewingReceipt->original_name }}"
                                class="max-w-full max-h-[60vh] rounded-xl shadow-lg object-contain">
                        </div>
                    @endif

                    <!-- Receipt Details -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 dark:bg-zinc-800 rounded-xl">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">File Size</p>
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ $viewingReceipt->file_size_formatted }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $viewingReceipt->status_badge_class }} text-white">
                                {{ $viewingReceipt->status_text }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Description</p>
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ $viewingReceipt->description ?: 'No description' }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-between p-6 border-t border-gray-200 dark:border-zinc-700">
                <a href="{{ Storage::url($viewingReceipt?->file_path) }}"
                    download="{{ $viewingReceipt?->original_name }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download
                </a>
                <button wire:click="closeViewModal"
                    class="px-4 py-2 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-all duration-200">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
