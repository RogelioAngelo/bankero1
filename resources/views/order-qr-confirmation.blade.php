@extends('layouts.app')
@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-body text-center">
                @if($status === 'invalid')
                    <h3>Invalid QR</h3>
                    <p>The QR token you scanned is invalid.</p>
                @elseif($status === 'already_scanned')
                    <h3>Already Confirmed</h3>
                    <p>This order was already marked as received on {{ optional($order->qr_scanned_at)->toDayDateTimeString() }}.</p>
                @elseif($status === 'ok')
                    <h3>Order Confirmed</h3>
                    <p>Thank you — this order has been marked as received.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
