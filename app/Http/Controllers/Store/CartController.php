<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', ['items'=>[], 'subtotal'=>0, 'count'=>0]);
        return view('cart.index', compact('cart'));
    }

    public function add(Request $request, Product $produto)
    {
        $qty = max(1, (int)$request->input('qty', 1));

        $cart = session('cart', ['items'=>[], 'subtotal'=>0, 'count'=>0]);
        $items = $cart['items'];

        $line = $items[$produto->id] ?? [
            'id'    => $produto->id,
            'name'  => $produto->name,
            'price' => (float)$produto->price,
            'image' => $produto->cover_url,
            'qty'   => 0,
        ];
        $line['qty'] = min(($line['qty'] + $qty), max(0, (int)$produto->stock ?: 9999));
        $items[$produto->id] = $line;

        $cart = $this->recalc($items);
        session(['cart' => $cart]);

        return redirect()->route('cart.index')->with('status', 'Produto adicionado ao carrinho.');
    }

    public function update(Request $request, Product $produto)
    {
        $qty = max(0, (int)$request->input('qty', 1));

        $cart = session('cart', ['items'=>[], 'subtotal'=>0, 'count'=>0]);
        $items = $cart['items'];

        if (isset($items[$produto->id])) {
            if ($qty === 0) unset($items[$produto->id]);
            else {
                $items[$produto->id]['qty'] = min($qty, max(0, (int)$produto->stock ?: 9999));
            }
        }
        $cart = $this->recalc($items);
        session(['cart' => $cart]);

        return back();
    }

    public function remove(Product $produto)
    {
        $cart = session('cart', ['items'=>[], 'subtotal'=>0, 'count'=>0]);
        $items = $cart['items']; unset($items[$produto->id]);
        $cart = $this->recalc($items);
        session(['cart' => $cart]);

        return back();
    }

    public function clear()
    {
        session()->forget('cart');
        return back();
    }

    private function recalc(array $items): array
    {
        $subtotal = 0; $count = 0;
        foreach ($items as $it) { $subtotal += $it['price'] * $it['qty']; $count += $it['qty']; }
        return ['items'=>$items, 'subtotal'=>round($subtotal,2), 'count'=>$count];
    }
}
