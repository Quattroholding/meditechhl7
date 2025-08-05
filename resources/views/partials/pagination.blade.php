<!-- Pagination -->
<div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
    <div>
        <p class="text-muted mb-0">
            Mostrando del {{ $data->firstItem() }} al {{ $data->lastItem() }}
            de {{ $data->total() }} resultados
        </p>
    </div>
    <div>
        {{ $data->links('vendor.pagination.custom-pagination') }}
    </div>
</div>
