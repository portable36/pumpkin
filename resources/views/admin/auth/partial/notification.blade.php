@if (count($errors ?? []) > 0)
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li class="reset-error-msg">{!! $error !!}</li>
            @endforeach
        </ul>
    </div>
@endif

@foreach (['success', 'danger', 'fail', 'warning', 'info'] as $msg)
    @if($message = Session::get($msg))
        <div class="alert-dismissible fade show alert alert-{{ $msg == 'fail' ? 'danger' : $msg }}" role="alert">
            <strong>{!! $message !!}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @break
    @endif
@endforeach
