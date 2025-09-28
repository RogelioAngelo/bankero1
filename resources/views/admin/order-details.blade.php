@extends('layouts.admin')
@section('content')
    <style>
        .table-transaction>tbody>tr:nth-of-type(odd) {
            --bs-table-accent-bg: #fff !important;
        }
    </style>
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Order Details</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Order Details</div>
                    </li>
                </ul>
            </div>

            {{-- details --}}
            <div class="wg-box">
                <div class="flex items-center justify-between gap10 flex-wrap">
                    <div class="wg-filter flex-grow">
                        <h5>Ordered Details</h5>
                    </div>
                    <a class="tf-button style-1 w208" href="{{ route('admin.orders') }}">Back</a>
                    <a class="tf-button style-2 w208" href="{{ route('admin.order.packslip', $order->id) }}">Download
                        Pack-slip</a>
                </div>
                <div class="table-responsive">
                    @if (Session::has('status'))
                        <p class="alert alert-success">{{ Session::get('status') }} </p>
                    @endif
                    <table class="table table-striped table-bordered">
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
            {{-- items --}}
            <div class="wg-box mt-5">
                <div class="flex items-center justify-between gap10 flex-wrap">
                    <div class="wg-filter flex-grow">
                        <h5>Ordered Items</h5>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-center">SKU</th>
                                <th class="text-center">Category</th>
                                <th class="text-center">Brand</th>
                                <th class="text-center">Options</th>
                                <th class="text-center">Return Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orderItems as $item)
                                <tr>
                                    <td class="pname">
                                        @php
                                            $product = $item->product ?? null;
                                        @endphp
                                        <div style="display:flex;align-items:center;gap:12px;">
                                            @if($product && !empty($product->image))
                                                <img src="{{ asset('uploads/products/thumbnails/' . $product->image) }}" alt="" style="width:60px;height:60px;object-fit:cover;border:1px solid #eee;padding:2px;" />
                                            @endif
                                            <div style="min-width:0;">
                                                <div style="font-weight:600;">{{ $product->name ?? ('Product #' . ($item->product_id ?? '')) }}</div>
                                                @if($product && !empty($product->short_description))
                                                    <div style="font-size:12px;color:#666;">{{ \Illuminate\Support\Str::limit(strip_tags($product->short_description), 80) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">${{ $item->price }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-center">{{ $product->SKU ?? ($item->sku ?? '') }}</td>
                                    <td class="text-center">{{ $product->category->name ?? '' }}</td>
                                    <td class="text-center">{{ $product->brand->name ?? '' }}</td>
                                    <td class="text-center">
                                        @php
                                            $opts = $item->options;
                                            $optsArr = null;
                                            if (!empty($opts)) {
                                                if (is_string($opts) && (json_decode($opts, true) !== null)) {
                                                    $optsArr = json_decode($opts, true);
                                                } elseif (is_array($opts)) {
                                                    $optsArr = $opts;
                                                }
                                            }
                                        @endphp
                                        @if($optsArr && is_array($optsArr))
                                            @foreach($optsArr as $k=>$v)
                                                <div style="font-size:12px">{{ $k }}: {{ is_array($v) ? json_encode($v) : $v }}</div>
                                            @endforeach
                                        @else
                                            {{ $item->options ?? '' }}
                                        @endif
                                    </td>
                                    <td class="text-center">{{ isset($item->rstatus) ? ($item->rstatus ? 'Returned' : '—') : ($item->return_status ?? '') }}</td>
                                    <td class="text-center">-</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
                            <th>Payment Status</th>
                            <td>
                                @if (isset($order->payment_status) && $order->payment_status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @else
                                    <span class="badge bg-secondary">Unpaid</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>


            <div class="wg-box mt-5">
                <h5>Update Order Status</h5>
                <form action="{{ route('admin.order.status.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="select">
                                <select id="order_status" name="order_status">
                                    <option value="ordered" {{ $order->status == 'ordered' ? 'selected' : '' }}>Ordered
                                    </option>
                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered
                                    </option>
                                    <option value="canceled" {{ $order->status == 'canceled' ? 'selected' : '' }}>Canceled
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary tf-button w208">Update Status</button>
                        </div>
                    </div>
                </form>
            </div>
            {{-- <div class="wg-box mt-5">
                <h5>Order QR</h5>
                <p>Only admin can view this QR. You may print or stick it on the package.</p>
                @if ($order->qr_token)
                    <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
                        @if (!empty($qrSvg))
                            <div style="width:200px;height:200px;border:1px solid #eee;padding:6px;background:#fff;">
                                {!! $qrSvg !!}</div>
                        @elseif(!empty($qrDataUri))
                            <img src="{{ $qrDataUri }}" alt="Order QR"
                                style="width:200px;height:200px;border:1px solid #eee;padding:6px;background:#fff;" />
                        @else
                            <img src="{{ route('admin.order.qr.image', $order->id) }}" alt="Order QR"
                                style="width:200px;height:200px;border:1px solid #eee;padding:6px;background:#fff;" />
                        @endif
                        <div>
                            <p><strong>Token:</strong> <code>{{ $order->qr_token }}</code></p>
                            <p><a href="{{ route('admin.order.qr.image', $order->id) }}"
                                    class="btn btn-success">Download</a></p>
                            <p><a href="{{ route('order.qr.scan', ['token' => $order->qr_token]) }}" target="_blank"
                                    class="btn btn-primary">Open Scan URL</a></p>
                        </div>
                    </div>
                @else
                    <p>No QR assigned to this order.</p>
                @endif
            </div> --}}
            <div class="wg-box mt-5">
                <h5>Order QR</h5>
                <p class="mb-3">Only admin can view this QR. You may print or stick it on the package.</p>
                @if ($order->qr_token)
                    <div class="order-qr-card" style="display:flex;gap:20px;align-items:center;flex-wrap:wrap;">
                        <div
                            style="background:#fff;border-radius:8px;padding:12px;border:1px solid #e6e6e6;box-shadow:0 2px 6px rgba(0,0,0,0.04);">
                            @if (!empty($qrDataUri))
                                <img id="order-qr-img" src="{{ $qrDataUri }}" alt="Order QR"
                                    style="width:220px;height:220px;display:block;" />
                            @else
                                <div
                                    style="width:220px;height:220px;display:flex;align-items:center;justify-content:center;color:#999;border:1px dashed #ddd;">
                                    No QR</div>
                            @endif
                        </div>

                        <div style="flex:1;min-width:220px;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <h6 style="margin:0;">Order #{{ $order->id }}</h6>
                                @if ($order->status == 'delivered')
                                    <span class="badge bg-success">Delivered</span>
                                @elseif($order->status == 'canceled')
                                    <span class="badge bg-danger">Canceled</span>
                                @else
                                    <span class="badge bg-secondary">Ordered</span>
                                @endif
                            </div>

                            <p style="margin:8px 0 12px;color:#555;">Scan the QR to confirm delivery. This QR is unique to
                                the order.</p>

                            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                <button type="button" class="btn btn-outline-secondary" onclick="copyToken()">Copy
                                    Token</button>
                                @if (!empty($qrDataUri))
                                    <a href="{{ route('admin.order.qr.image', $order->id) }}"
                                        class="btn btn-success">Download</a>
                                @endif
                                <a href="{{ route('admin.order.packslip', $order->id) }}" target="_blank"
                                    class="btn btn-primary">Print Pack-slip</a>
                                <a href="{{ route('order.qr.scan', ['token' => $order->qr_token]) }}" target="_blank"
                                    class="btn btn-outline-primary">Open Scan URL</a>
                            </div>

                            <div style="margin-top:12px;border-top:1px solid #f0f0f0;padding-top:12px;">
                                <div style="font-size:13px;color:#333;">Token</div>
                                <div style="display:flex;align-items:center;gap:8px;margin-top:6px;">
                                    <code id="order-qr-token"
                                        style="background:#f8f9fb;padding:6px 8px;border-radius:4px;">{{ $order->qr_token }}</code>
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        onclick="copyToken()">Copy</button>
                                </div>
                                @if ($order->qr_scanned_at)
                                    <div style="margin-top:8px;color:green;font-size:13px;">Scanned at:
                                        {{ $order->qr_scanned_at }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <script>
                        function copyToken() {
                            const tokenEl = document.getElementById('order-qr-token');
                            if (!tokenEl) return;
                            const token = tokenEl.innerText || tokenEl.textContent;
                            if (navigator.clipboard && navigator.clipboard.writeText) {
                                navigator.clipboard.writeText(token).then(() => {
                                    alert('Token copied to clipboard');
                                }).catch(() => {
                                    alert('Unable to copy');
                                });
                            } else {
                                const ta = document.createElement('textarea');
                                ta.value = token;
                                document.body.appendChild(ta);
                                ta.select();
                                try {
                                    document.execCommand('copy');
                                    alert('Token copied to clipboard');
                                } catch (e) {
                                    alert('Unable to copy');
                                }
                                document.body.removeChild(ta);
                            }
                        }
                    </script>
                @else
                    <p>No QR assigned to this order.</p>
                @endif
            </div>
        </div>
    </div>
    </div>
@endsection
