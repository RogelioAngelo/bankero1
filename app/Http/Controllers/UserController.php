<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->orderBy('created_at', 'DESC')->paginate(10);

        $orders_count = Order::where('user_id', Auth::id())->count();
        $total_spent = Order::where('user_id', Auth::id())->sum('total');

        return view('user.index', compact('orders', 'orders_count', 'total_spent'));
    }

    public function orders(\Illuminate\Http\Request $request)
    {
        $status = $request->query('status');

        $query = Order::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC');

        if ($status === 'delivered') {
            $query->where('status', 'delivered');
        } elseif ($status === 'canceled') {
            $query->where('status', 'canceled');
        } elseif ($status === 'ordered') {
            // treat 'ordered' as anything not delivered or canceled
            $query->whereNotIn('status', ['delivered', 'canceled']);
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('user.orders', compact('orders', 'status'));
    }

    public function order_details($order_id)
    {
        $order = Order::where('user_id', Auth::user()->id)
            ->where('id', $order_id)
            ->first();
        if ($order) {
            $orderItems = OrderItem::where('order_id', $order->id)->orderBy('id')->paginate(12);
            $transaction = Transaction::where('order_id', $order->id)->first();
            return view('user.order-details', compact('order', 'orderItems', 'transaction'));
        } else {
            return redirect()->route('login');
        }
    }

    public function order_canceled(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->status = 'canceled';
        $order->canceled_date = Carbon::now();
        $order->save();
        return back()->with('status', 'Order has been canceled Successfully!');
    }
    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpg,jpeg,png',
        ]);

        $user = auth()->user();

        if ($request->hasFile('profile_photo')) {
            // Store in storage/app/public/profile_photos
            $path = $request->file('profile_photo')->store('profile_photos', 'public');

            // Update user profile
            $user->profile_photo = $path;
            $user->save();
        }

        return back()->with('status', 'Profile photo updated successfully!');
    }
}
