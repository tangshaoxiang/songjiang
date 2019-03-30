@if( session('success'))
    <div class="form-group">
        <div class="alert alert-info">
            <ul style="">
                <li>{{ session('success') }}</li>
            </ul>
        </div>
    </div>
@endif

@if( \Session('errors') )
    <div class="form-group">
        <div class="alert alert-warning">
            <ul>
                @foreach(\Session('errors')->all() as $key => $value)
                    <li>{{ $value }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif