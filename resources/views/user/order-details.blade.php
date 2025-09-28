@extends('layouts.app')
@section('content')
<style>
    .pt-90 {
      padding-top: 90px !important;
    }

    .pr-6px {
      padding-right: 6px;
      text-transform: uppercase;
    }

    .my-account .page-title {
      font-size: 1.5rem;
      font-weight: 700;
      text-transform: uppercase;
      margin-bottom: 40px;
      border-bottom: 1px solid;
      padding-bottom: 13px;
    }

    .my-account .wg-box {
      display: -webkit-box;
      display: -moz-box;
      display: -ms-flexbox;
      display: -webkit-flex;
      display: flex;
      padding: 24px;
      flex-direction: column;
      gap: 24px;
      border-radius: 12px;
      background: var(--White);
      box-shadow: 0px 4px 24px 2px rgba(20, 25, 38, 0.05);
    }

    .bg-success {
      background-color: #40c710 !important;
    }

    .bg-danger {
      background-color: #f44032 !important;
    }

    .bg-warning {
      background-color: #f5d700 !important;
      color: #000;
    }

    .table-transaction>tbody>tr:nth-of-type(odd) {
      --bs-table-accent-bg: #fff !important;

    }

    .table-transaction th,
    .table-transaction td {
      padding: 0.625rem 1.5rem .25rem !important;
      color: #000 !important;
    }

    .table> :not(caption)>tr>th {
      padding: 0.625rem 1.5rem .25rem !important;
      background-color: #6a6e51 !important;
    }

    .table-bordered>:not(caption)>*>* {
      border-width: inherit;
      line-height: 32px;
      font-size: 14px;
      border: 1px solid #e1e1e1;
      vertical-align: middle;
    }

    .table-striped .image {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 50px;
      height: 50px;
      flex-shrink: 0;
      border-radius: 10px;
      overflow: hidden;
    }

    .table-striped td:nth-child(1) {
      min-width: 250px;
      padding-bottom: 7px;
    }

    .pname {
      display: flex;
      gap: 13px;
    }

    .table-bordered> :not(caption)>tr>th,
    .table-bordered> :not(caption)>tr>td {
      border-width: 1px 1px;
      border-color: #6a6e51;
    }
  </style>
    <main class="pt-90" style="padding-top: 0px;">
        <div class="mb-2"></div>
        <section class="my-account container">
            <h2 class="page-title">Order Details</h2>
            <div class="row">
                <div class="col-lg-2">
                    @include('user.account-nav')
                </div>

                <div class="col-lg-10">
                    <div class="wg-box">
                        <div class="flex items-center justify-between gap10 flex-wrap">
                            <div class="wg-filter flex-grow">
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <h5>Ordered Details</h5>
                                </div>
                                <div class="col-6 text-right">
                                    <a class="btn btn-sm btn-danger" href="{{ route('user.orders') }}">Back</a>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            @if (Session::has('status'))
                                <p class="alert alert-success">{{Session::get('status')}} </p>
                            @endif
                            <table class="table table-bordered ">
                                <tr>
                                    <th>Order No.</th>
                                    <td>{{ $order->id }} </td>
                                    <th>Mobile</th>
                                    <td>{{ $order->phone }} </td>
                                    <th>Zip Code</th>
                                    <td>{{ $order->zip }} </td>
                                </tr>
                                <tr>
                                    <th>Order Date</th>
                                    <td>{{ $order->created_at }} </td>
                                    <th>Delivered Date</th>
                                    <td>{{ $order->delivered_date }} </td>
                                    <th>Canceled Date</th>
                                    <td>{{ $order->canceled_date }} </td>
                                </tr>
                                <tr>
                                    <th>Order Status</th>
                                    <td colspan="5">
                                        @if ($order->status == 'delivered')
                                            <span class="badge bg-success">Delivered</span>
                                        @elseif($order->status == 'canceled')
                                            <span class="badge bg-danger">Canceled</span>
                                        @else
                                            <span class="badge bg-warning">Ordered</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="wg-box">
                        <div class="flex items-center justify-between gap10 flex-wrap">
                            <div class="wg-filter flex-grow">
                                <h5>Ordered Items</h5>
                            </div>
                        </div>
                        <style>
                            .order-items-grid { display: flex; flex-wrap: wrap; gap: 16px; }
                            .order-item-card { background: #fff; border: 1px solid #e6e6e6; border-radius: 12px; padding: 12px; display:flex; gap:12px; align-items:flex-start; }
                            .order-item-media { width: 84px; height: 84px; flex-shrink:0; border-radius:8px; overflow:hidden; display:flex; align-items:center; justify-content:center; }
                            .order-item-media img { width:100%; height:100%; object-fit:cover; }
                            .order-item-body { flex:1; display:flex; flex-direction:column; gap:6px; }
                            .order-item-meta { display:flex; gap:12px; flex-wrap:wrap; font-size:13px; color:#666; }
                            .order-item-actions { display:flex; gap:8px; align-items:center; }
                            .line-price { font-weight:600; }
                        </style>

                        <div class="order-items-grid">
                            @foreach ($orderItems as $item)
                                <div class="order-item-card col-12">
                                    <div class="order-item-media">
                                        <img src="{{ asset('uploads/products/thumbnails/' . ($item->product->image ?? 'placeholder.png')) }}" alt="{{ $item->product->name ?? 'Product' }}">
                                    </div>
                                    <div class="order-item-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <a href="{{ isset($item->product) ? route('shop.product.details', ['product_slug' => $item->product->slug]) : '#' }}" target="_blank" class="h6 mb-1">{{ $item->product->name ?? 'Product' }}</a>
                                                <div class="order-item-meta">
                                                    <span>SKU: {{ $item->product->SKU ?? ($item->sku ?? '-') }}</span>
                                                    <span>Qty: {{ $item->quantity }}</span>
                                                    <span>Price: ${{ number_format((float)$item->price,2) }}</span>
                                                    <span class="line-price">Line: ${{ number_format((float)$item->price * intval($item->quantity),2) }}</span>
                                                </div>
                                            </div>
                                            <div class="order-item-actions text-end">
                                                <div class="mb-2">{{ $item->rstatus == 0 ? 'Return: No' : 'Return: Yes' }}</div>
                                                <a href="{{ route('user.order.details', ['order_id' => $order->id]) }}#item-{{ $item->id }}" class="btn btn-outline-secondary btn-sm" title="View item"><i class="icon-eye"></i></a>
                                            </div>
                                        </div>

                                        <div class="order-item-meta mt-2">
                                            <span>Category: {{ $item->product->category->name ?? '-' }}</span>
                                            <span>Brand: {{ $item->product->brand->name ?? '-' }}</span>
                                            <span>Options: {!! nl2br(e($item->options ?? '-')) !!}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="divider"></div>
                        <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                            {{ $orderItems->links('pagination::bootstrap-5') }}
                        </div>
                    </div>

                    <div class="wg-box mt-5">
                        <h5>Shipping Address</h5>
                        <div class="my-account__address-item col-md-6">
                            <div class="my-account__address-item__detail">
                                <p>{{ $order->address }}</p>
                                <p>{{ $order->locality }}</p>
                                <p>{{ $order->city }}, {{ $order->country }}</p>
                                <p>{{ $order->landmark }}</p>
                                <p>{{ $order->zip }}</p>
                                <p>Mobile: {{ $order->phone }}</p>

                            </div>
                        </div>
                    </div>

                    <div class="wg-box mt-5">
                        <h5>Transactions</h5>
                        <table class="table table-striped table-bordered table-transaction">
                            <tbody>
                                <tr>
                                    <th>Subtotal</th>
                                    <td>${{ $order->subtotal }} </td>
                                    <th>Tax</th>
                                    <td>${{ $order->tax }}</td>
                                    <th>Discount</th>
                                    <td>${{ $order->discount }}</td>
                                </tr>
                                <tr>
                                    <th>Total</th>
                                    <td>${{ $order->total }}</td>
                                    <th>Payment Mode</th>
                                    <td>{{ $transaction->mode }}</td>
                                    <th>Status</th>
                                    <td>
                                        @if ($transaction->status == 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif ($transaction->status == 'declined')
                                            <span class="badge bg-danger">Declined</span>
                                        @elseif ($transaction->status == 'refunded')
                                            <span class="badge bg-secondary">Refunded</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @if ($order->status=='ordered')
                    <div class="wg-box mt-5 text-right">
                        <form action="{{route('user.order.cancel')}}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="order_id" value="{{$order->id}}">
                            <button type="button" class="btn btn-danger cancel-order">Cancel Order</button>
                        </form>
                    </div>
                    @endif
                    @if ($transaction && $transaction->status == 'pending' && ($order->payment_status ?? 'unpaid') != 'paid')
                    <div class="wg-box mt-3 text-right">
                        <form action="{{ route('order.settle', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary">Settle Payment</button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </section>
    </main>
@endsection

@push('scripts')
<script>
    $(function(){
        $('.cancel-order').on('click',function(e){
            e.preventDefault();

            var form = $(this).closest('form');

            swal({
                title: "Are you sure?",
                text: "You want to Cancel this Order?",
                type: "warning",
                buttons:["No","Yes"],
                confirmButtonColor:'#dc3545',
            }).then(function(result){
                // SweetAlert (v1) may return true/false; SweetAlert2 returns an object with isConfirmed
                if (result === true || (result && result.isConfirmed)){
                    form.submit();
                }
            }).catch(function(err){
                // ignore or log
                console.error('Cancel dialog error', err);
            });
        });
    });
</script>
@endpush
