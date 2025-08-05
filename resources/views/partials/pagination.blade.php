<!-- Pagination -->
<div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
    <div>
        <p class="text-muted mb-0">
            {{__('pagination.showing')}} {{ $data->firstItem() }} {{__('pagination.to')}} {{ $data->lastItem() }} {{__('pagination.of')}} {{ $data->total() }} {{__('pagination.results')}}
        </p>
    </div>
    <div>
        {{ $data->links('vendor.pagination.custom-pagination') }}
    </div>
</div>
