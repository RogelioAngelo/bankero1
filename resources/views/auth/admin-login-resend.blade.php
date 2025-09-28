@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Resend Confirmation Email</div>
                <div class="card-body">
                    @if($errors->has('resend'))
                        <div class="alert alert-danger">{{ $errors->first('resend') }}</div>
                    @endif
                    @if(session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif
                    <form method="POST" action="{{ route('admin.login.resend') }}">
                        @csrf
                        <p>If you didn't receive the email, click the button below to resend.</p>
                        <button class="btn btn-primary">Resend</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Resend Admin Confirmation</div>
                <div class="card-body">
                    <p>Click the button below to resend the admin confirmation email.</p>
                    @if(session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif
                    @php
                        $pendingId = session('admin_login_pending_id');
                        $info = null;
                        if ($pendingId) {
                            $info = session('admin_resend_info_' . $pendingId);
                        }
                    @endphp

                    @if(isset($info['blocked_until']) && $info['blocked_until'] > now()->timestamp)
                        @php $remaining = $info['blocked_until'] - now()->timestamp; @endphp
                        <div class="alert alert-warning">You have reached the resend limit. Please wait <span id="countdown">{{ gmdate('i:s', $remaining) }}</span> before trying again.</div>
                    @else
                        <form method="POST" action="{{ route('admin.login.resend') }}">
                            @csrf
                            <button class="btn btn-primary">Resend Confirmation</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if(isset($info['blocked_until']) && $info['blocked_until'] > now()->timestamp)
<script>
    (function(){
        var remaining = {{ $info['blocked_until'] - now()->timestamp }};
        var el = document.getElementById('countdown');
        var iv = setInterval(function(){
            if (remaining <= 0) { clearInterval(iv); location.reload(); return; }
            remaining--; var mm = Math.floor(remaining/60); var ss = remaining % 60; el.textContent = (mm<10? '0'+mm:mm) + ':' + (ss<10? '0'+ss:ss);
        }, 1000);
    })();
</script>
@endif
@endpush
