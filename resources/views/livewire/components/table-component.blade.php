<div class="mx-auto mt-6">
    <!-- Loading Overlay -->
    <div wire:loading>
        <livewire:components.spinner-component />
    </div>

    <!-- Card -->
    <div class="relative py-5 px-2 bg-white rounded-lg ">
        <!-- Header Controls -->
        <div class="flex flex-col items-center justify-between gap-4 mb-4 md:flex-row ">
            <!-- Items Per Page -->
            <div class="flex items-center gap-2">
                <label for="itemsPerPage" class="text-sm font-medium text-gray-700 ">Items per page:</label>
                <select wire:model.live="itemsPerPage" id="itemsPerPage"
                    class="w-24 px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500  ">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>

            <!-- Search + Create -->
            <div class="flex flex-col items-center gap-2 md:flex-row">
                <input type="text" placeholder="{{ $searchPlaceHolder }}" wire:model.live.debounce.500ms="search"
                    class="w-64 px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500   " />

                @if ($showCreateButton)
                <a wire:navigate href="{{ route($viewRoute, 'createForm') }}" class="btn btn-primary">
                    <i class="mr-2 fas fa-user-plus"></i> Create
                </a>
                @endif

            </div>
        </div>

        <!-- Table -->
        <div class="w-full overflow-x-auto bg-white">
            <div class="min-w-[1000px]"> <!-- Force min width to allow scroll -->
                <table class="min-w-full divide-y divide-gray-200 ">
                    <thead class="bg-gray-100 ">
                        <tr>
                            <th class="px-4 py-2 text-xs font-semibold text-left text-gray-600 ">#</th>
                            @foreach ($columns as $column)
                            <th class="px-4 py-2 text-xs font-semibold text-left text-gray-600 ">
                                {{ strtoupper(str_replace(['_', '.'], ' ', $column)) }}
                            </th>
                            @endforeach
                            <th class="px-4 py-2 text-xs font-semibold text-left text-gray-600 ">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white  ">
                        @forelse ($data as $item)
                        <tr class="transition cursor-pointer hover:bg-gray-100 "
                            onclick="window.location='{{ route($viewRoute, $item->id) }}'">
                            <td class="px-4 py-2 text-sm text-gray-800 ">{{ $loop->iteration }}</td>
                            @foreach ($columns as $column)
                            <td class="px-4 py-2 text-sm text-gray-700 ">
                                @if (str_contains($column, '.'))
                                {{ data_get($item, $column) ?? 'N/A' }}
                                @else
                                {{ $item->$column }}
                                @endif
                            </td>
                            @endforeach
                            <td class="px-4 py-2">
                                <a wire:navigate href="{{ route($viewRoute, $item->id) }}"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-blue-500 hover:bg-blue-600 rounded-md shadow">
                                    <i class="fas fa-bars"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ count($columns) + 2 }}"
                                class="px-4 py-4 text-sm text-center text-gray-500 ">
                                No records found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


        <!-- Pagination -->
       <div class="flex items-center justify-between mt-4 text-sm bg-white text-gray-600">
    <div>
        {{ $data->links('pagination::tailwind') }}
    </div>
    
</div>
    </div>
</div>