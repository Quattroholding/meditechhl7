@if ($paginator->hasPages())
    <nav>
        <ul class="pagination justify-content-center">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link" aria-hidden="true">&laquo;</span>
                </li>
            @else
                <li class="page-item">
                    <button class="page-link" wire:click="previousPage" rel="prev" aria-label="@lang('pagination.previous')">&laquo;</button>
                </li>
            @endif

            {{-- First Page --}}
            @if ($paginator->currentPage() > 3)
                <li class="page-item">
                    <button class="page-link" wire:click="gotoPage(1)">1</button>
                </li>
                @if ($paginator->currentPage() > 4)
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">...</span>
                    </li>
                @endif
            @endif

            {{-- Current Page with 2 pages on each side --}}
            @for ($page = max(1, $paginator->currentPage() - 2); $page <= min($paginator->lastPage(), $paginator->currentPage() + 2); $page++)
                @if ($page == $paginator->currentPage())
                    <li class="page-item active" aria-current="page">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <button class="page-link" wire:click="gotoPage({{ $page }})">{{ $page }}</button>
                    </li>
                @endif
            @endfor

            {{-- Last Page --}}
            @if ($paginator->currentPage() < $paginator->lastPage() - 2)
                @if ($paginator->currentPage() < $paginator->lastPage() - 3)
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">...</span>
                    </li>
                @endif
                <li class="page-item">
                    <button class="page-link" wire:click="gotoPage({{ $paginator->lastPage() }})">{{ $paginator->lastPage() }}</button>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <button class="page-link" wire:click="nextPage" rel="next" aria-label="@lang('pagination.next')">&raquo;</button>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link" aria-hidden="true">&raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif