<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductPublicController extends Controller
{
    public function show(string $ref)
    {
        // Busca por slug (texto) OU id (numérico), sempre apenas produtos ativos
        $product = Product::active()
            ->when(is_numeric($ref), fn($q) => $q->where('id', $ref),
                                 fn($q) => $q->where('slug', $ref))
            ->firstOrFail();

        $related = Product::active()
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(8)
            ->get();

        return view('produto.show', compact('product', 'related'));
    }
}
