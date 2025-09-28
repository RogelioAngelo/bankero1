@extends('layouts.app')

@section('content')
<section class="container py-5">
    <div class="card mx-auto" style="max-width:640px;">
        <div class="card-body text-center">
            <h3 class="mb-3">Payment processing</h3>
            <p>Your payment is being processed. Please wait a moment — we'll show the confirmation page as soon as payment is confirmed.</p>
            <p class="text-muted">If you are not redirected automatically, you can refresh this page.</p>
            <div class="mt-4">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Back to shop</a>
            </div>
        </div>
    </div>
</section>
@endsection

        @push('scripts')
        <script>
            (function(){
                // Poll the transaction status endpoint until an order is created.
                const txId = {{ json_encode(optional($transaction)->id) }};
                if (!txId) return;

                let interval = 3000; // start 3s
                let attempts = 0;
                const maxAttempts = 40; // safe guard ~2 minutes

                function check() {
                    attempts++;
                    fetch('/transaction/' + txId + '/status', { credentials: 'same-origin' })
                        .then(r => r.json())
                        .then(json => {
                            if (json && json.order_id) {
                                // order created — redirect to order confirmation
                                window.location = '{{ route('cart.order.confirmation') }}';
                                return;
                            }
                            if (attempts >= maxAttempts) return;
                            // exponential backoff up to 10s
                            interval = Math.min(10000, Math.round(interval * 1.2));
                            setTimeout(check, interval);
                        })
                        .catch(() => {
                            if (attempts >= maxAttempts) return;
                            setTimeout(check, interval);
                        });
                }

                // start polling after a short delay to let the server settle
                setTimeout(check, 1500);
            })();
        </script>
        @endpush
