<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Feature\Order;
use App\Models\Master\Product;
use App\Repositories\CrudRepositories;
use App\Services\Feature\OrderService;
use App\Services\Midtrans\CreateSnapTokenService;
use Illuminate\Http\Request;

class TransacationController extends Controller
{   
    protected $orderService;
    protected $order;
    public function __construct(OrderService $orderService,Order $order)
    {
        $this->orderService = $orderService;
        $this->order = new CrudRepositories($order);
    }

    public function index()
    {
        $data['orders'] = $this->orderService->getUserOrder(auth()->user()->id);
        return view('frontend.transaction.index',compact('data'));
    }

    public function show($invoice_number)
    {
        $data['order'] = $this->order->Query()->where('invoice_number',$invoice_number)->first();
        $snapToken = $data['order']->snap_token;
        if (empty($snapToken)) {
            // Jika snap token masih NULL, buat token snap dan simpan ke database
            $midtrans = new CreateSnapTokenService($data['order']);
            $snapToken = $midtrans->getSnapToken();
            $data['order']->snap_token = $snapToken;
            $data['order']->save();
        }
        return view('frontend.transaction.show',compact('data'));
    }

    public function received($invoice_number)
    {
        $this->order->Query()->where('invoice_number',$invoice_number)->first()->update(['status' => 3]);
        return back()->with('success',__('message.order_received'));
    }

    public function canceled($invoice_number)
    {
        $order = $this->order->Query()->where('invoice_number', $invoice_number)->first();

if ($order) {
    $order->update(['status' => 4]);

    foreach ($order->orderDetail as $orderDetail) {
        $product = Product::find($orderDetail->product_id);

        if ($product) {
            $product->qty += $orderDetail->qty;
            $product->save();
        }
    }

    return back()->with('success', __('message.order_canceled'));
}
    }
    public function updateOrderStatus(Request $request)
    {
        $invoiceNumber = $request->input('invoiceNumber');

        // Find the order based on the invoice number
        $order = Order::where('invoice_number', $invoiceNumber)->first();

        if ($order) {
            // Update the order status
            $order->status = 1;
            $order->save();

            return response('sukses', 200);
        }

        return response('Order not found.', 404);
    }
}
