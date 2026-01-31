@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between px-4 py-4 bg-white rounded-lg shadow-sm border border-gray-100">
        <!-- Mobile view -->
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-200 cursor-default rounded-lg leading-5 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-lg hover:bg-blue-50 hover:text-blue-700 hover:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent active:bg-blue-100 transition-all duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2.5 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-lg hover:bg-blue-50 hover:text-blue-700 hover:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent active:bg-blue-100 transition-all duration-200 shadow-sm">
                    {!! __('pagination.next') !!}
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2.5 ml-3 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-200 cursor-default rounded-lg leading-5 transition-colors">
                    {!! __('pagination.next') !!}
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            @endif
        </div>

        <!-- Desktop view -->
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between ">
            <!-- Results info -->
            <div class="mx-20">
                <p class="text-sm text-gray-600 leading-5">
                    <span class="font-medium">{{ $paginator->firstItem() }}</span>
                    {!! __('to') !!}
                    <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    {!! __('of') !!}
                    <span class="font-medium text-gray-900">{{ $paginator->total() }}</span>
                    {!! __('results') !!}
                </p>
            </div>

            <!-- Page links -->
            <div>
                <span class="relative z-0 inline-flex items-center shadow-sm rounded-lg overflow-hidden border border-gray-200 bg-white">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}" class="relative inline-flex items-center px-3 py-2 text-gray-400 bg-gray-50 border-r border-gray-200 cursor-default">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-3 py-2 text-gray-600 bg-white border-r border-gray-200 hover:bg-blue-50 hover:text-blue-700 focus:z-10 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 active:bg-blue-100 transition-all duration-200" aria-label="{{ __('pagination.previous') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 border-r border-gray-200 bg-white">
                                {{ $element }}
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border-r border-blue-700">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border-r border-gray-200 hover:bg-gray-50 hover:text-blue-700 focus:z-10 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 active:bg-gray-100 transition-all duration-200" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3 py-2 text-gray-600 bg-white hover:bg-blue-50 hover:text-blue-700 focus:z-10 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 active:bg-blue-100 transition-all duration-200" aria-label="{{ __('pagination.next') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}" class="relative inline-flex items-center px-3 py-2 text-gray-400 bg-gray-50 cursor-default">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif