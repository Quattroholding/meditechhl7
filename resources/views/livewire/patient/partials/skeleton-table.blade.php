<!-- Skeleton Loading for Tables -->
<div class="card">
    <div class="card-body">
        <!-- Skeleton Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="skeleton-box" style="width: 150px; height: 20px;"></div>
            <div class="skeleton-box" style="width: 100px; height: 32px;"></div>
        </div>
        
        <!-- Skeleton Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        @for($i = 0; $i < 4; $i++)
                        <th>
                            <div class="skeleton-box" style="width: 120px; height: 16px;"></div>
                        </th>
                        @endfor
                        <th>
                            <div class="skeleton-box" style="width: 80px; height: 16px;"></div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @for($row = 0; $row < 5; $row++)
                    <tr>
                        @for($col = 0; $col < 4; $col++)
                        <td>
                            <div class="skeleton-box" style="width: {{ rand(80, 150) }}px; height: 14px;"></div>
                        </td>
                        @endfor
                        <td>
                            <div class="d-flex gap-2">
                                <div class="skeleton-box" style="width: 24px; height: 24px; border-radius: 4px;"></div>
                                <div class="skeleton-box" style="width: 24px; height: 24px; border-radius: 4px;"></div>
                            </div>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        
        <!-- Skeleton Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="skeleton-box" style="width: 200px; height: 16px;"></div>
            <div class="d-flex gap-2">
                @for($i = 0; $i < 5; $i++)
                <div class="skeleton-box" style="width: 32px; height: 32px; border-radius: 4px;"></div>
                @endfor
            </div>
        </div>
    </div>
</div>

<style>
.skeleton-box {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: skeleton-loading 1.5s infinite;
    border-radius: 4px;
}

@keyframes skeleton-loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

.skeleton-box + .skeleton-box {
    margin-top: 8px;
}
</style>