@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Confirm Admin Login</div>
                <div class="card-body">
                    <p>An email with a confirmation link was sent to your address. Please open it and confirm the login to continue.</p>
                    @if(session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif
                    <a href="{{ route('admin.login.resend.page') }}" class="btn btn-secondary">Resend Email</a>
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
                <div class="card-header">Confirm Admin Login</div>
                <div class="card-body">
                    <p>An email with a confirmation link has been sent to your address. Please open it and confirm the login to continue.</p>
                    @if(session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif
                    <a href="{{ route('admin.login.resend.page') }}" class="btn btn-secondary">Resend Email</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
