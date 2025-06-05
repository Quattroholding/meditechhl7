<div class="row" id="alert_messages">
    <div class="col-md-12">
        @if (count($errors->all()) > 0)
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <ul>
                    @foreach($errors->all() as $e)
                        <li/>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('message.error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {!! nl2br(session('message.error')) !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('message.warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {!! nl2br(session('message.warning')) !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('message.success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {!! nl2br(session('message.success')) !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>
</div>
