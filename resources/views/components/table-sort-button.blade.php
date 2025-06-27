@props(['sortDirection' => 'asc','sortField'=>'id','columnName'=>'id','title'=>'id'])

<button type="button" class="btn btn-link p-0 text-decoration-none text-dark fw-bold"  @empty(!$columnName)wire:click="sortBy('{{$columnName}}')@endempty">
    {{ $title }}
    @empty(!$columnName)
    @if($sortField === $columnName)
        @if($sortDirection === 'asc')
            <i class="fas fa-sort-up"></i>
        @else
            <i class="fas fa-sort-down"></i>
        @endif
    @else
        <i class="fas fa-sort"></i>
    @endif
    @endempty
</button>
