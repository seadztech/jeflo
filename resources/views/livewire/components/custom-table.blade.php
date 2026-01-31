<div class="relative p-5 overflow-x-auto shadow-md sm:rounded-lg">
    <!-- Loading Spinner -->
    <div wire:loading.delay.class="opacity-100"
         wire:loading.class.remove="opacity-0"
         class="absolute inset-0 z-50 flex items-center justify-center transition-opacity duration-300 bg-white opacity-0 bg-opacity-70 dark:bg-gray-800 dark:bg-opacity-70">
        <div class="w-8 h-8 border-4 border-blue-500 rounded-full border-t-transparent animate-spin"></div>
    </div>

    <!-- Table Controls -->
    <div class="flex flex-wrap items-center justify-between pb-4 mt-4 space-y-4 flex-column sm:flex-row sm:space-y-0">
        <!-- Items per page -->
        <div>
            <select wire:model.live="itemsPerPage"
                class="inline-flex items-center w-24 px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-blue-400">
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>

        <!-- Search -->
        @if($searchable)
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none rtl:inset-r-0 rtl:right-0 ps-3">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <input wire:model.live.debounce.500ms="search" type="text"
                class="block p-2 text-sm text-gray-900 border border-gray-300 rounded-lg ps-10 w-80 bg-gray-white focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="{{ $searchPlaceholder }}">
        </div>
        @endif

        <!-- Add Button -->
        @if($createRoute)
        <div>
            <button wire:click="createItem"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-500 dark:hover:bg-blue-600">
                <i class="mr-1 fa fa-plus"></i> {{ $addButtonText }}
            </button>
        </div>
        @endif
    </div>

    <!-- Table -->
    <div class="overflow-hidden border border-gray-200 rounded-lg dark:border-gray-700">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    @foreach($headers as $key => $header)
                        <th scope="col"
                            @if($sortable) wire:click="sortBy('{{ $key }}')" @endif
                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase cursor-pointer dark:text-gray-400">
                            <div class="flex items-center">
                                {{ $header['label'] }}
                                @if($sortable && $sortField === $key)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M{{ $sortDirection === 'asc' ? '19 9l-7 7-7-7' : '5 15l7-7 7 7' }}" />
                                    </svg>
                                @endif
                            </div>
                        </th>
                    @endforeach

                    @if($withActions)
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                            Actions
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                @forelse($items as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        @foreach($headers as $key => $header)
                            <td class="px-6 py-4 whitespace-nowrap @if($header['bold'] ?? false) font-medium text-gray-900 dark:text-white @endif">
                                @if(isset($header['format']) && $header['format'] === 'date')
                                    {{ $item->{$key}->format($header['format_options']['format'] ?? 'Y-m-d') }}
                                @else
                                    {{ $item->{$key} }}
                                @endif
                            </td>
                        @endforeach

                        @if($withActions)
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex space-x-2">
                                    @if($viewRoute)
                                        <button wire:click="viewItem({{ $item->{$primaryKey} }})"
                                                wire:loading.attr="disabled"
                                                class="px-3 py-1 text-sm text-white bg-blue-500 rounded hover:bg-blue-600">
                                            <i class="mr-1 fa fa-eye"></i> View
                                        </button>
                                    @endif
                                    @if($editRoute)
                                        <button wire:click="editItem({{ $item->{$primaryKey} }})"
                                                wire:loading.attr="disabled"
                                                class="px-3 py-1 text-sm text-white bg-yellow-500 rounded hover:bg-yellow-600">
                                            <i class="mr-1 fa fa-edit"></i> Edit
                                        </button>
                                    @endif
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) + ($withActions ? 1 : 0) }}" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            No records found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($pagination['links'])
        <div class="mt-4">
            {!! $pagination['links'] !!}
        </div>
    @endif
</div>
