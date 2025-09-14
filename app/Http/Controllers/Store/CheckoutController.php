<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user(); // garantido pelo middleware

        $cart = session('cart');
        abort_if(!$cart || empty($cart['items']), 400, 'Carrinho vazio.');

        $order = Order::create([
            'user_id'        => $user->id,                           // <- nunca nulo
            'status'         => 'pending',
            'total'          => $cart['subtotal'],
            'customer_name'  => $request->input('name',  $user->name),
            'customer_email' => $request->input('email', $user->email),
            'pix_txid'       => 'LJ'.\Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(10)),
        ]);

        foreach ($cart['items'] as $it) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $it['id'],
                'quantity'   => $it['qty'],
                'unit_price' => $it['price'],
                'total'      => $it['price'] * $it['qty'],
            ]);
        }

        session()->forget('cart');

        return redirect()->route('pix.show', $order);
    }
}
