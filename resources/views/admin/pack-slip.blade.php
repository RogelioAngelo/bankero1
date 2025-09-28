@extends('layouts.print')
@section('content')
<div style="font-family: Arial, Helvetica, sans-serif; font-size: 14px;">
    <h2>Pack Slip - Order #{{ $order->id }}</h2>
    <p><strong>Name:</strong> {{ $order->name }}</p>
    <p><strong>Phone:</strong> {{ $order->phone }}</p>
    <p><strong>Address:</strong> {{ $order->address }}, {{ $order->city }}, {{ $order->state }} {{ $order->zip }}</p>

    <h3>Items</h3>
    <table width="100%" style="border-collapse: collapse;">
        <thead>
            <tr>
                <th style="border:1px solid #ddd;padding:6px;text-align:left;">Product</th>
                <th style="border:1px solid #ddd;padding:6px;text-align:center;">Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orderItems as $item)
                <tr>
                    <td style="border:1px solid #ddd;padding:6px;">{{ $item->product->name }}</td>
                    <td style="border:1px solid #ddd;padding:6px;text-align:center;">{{ $item->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:20px;display:flex;gap:20px;align-items:center;">
        <div>
            @if(!empty($qrSvg))
                <div style="width:200px;height:200px;">{!! $qrSvg !!}</div>
            @else
                <img src="{{ route('admin.order.qr.image', $order->id) }}" alt="Order QR" style="width:200px;height:200px;" />
            @endif
            <p style="font-size:12px;">Scan to confirm delivery</p>
        </div>

        <div>
            <p><strong>Subtotal:</strong> ${{ $order->subtotal }}</p>
            <p><strong>Tax:</strong> ${{ $order->tax }}</p>
            <p><strong>Total:</strong> ${{ $order->total }}</p>
        </div>
    </div>
</div>
@endsection
