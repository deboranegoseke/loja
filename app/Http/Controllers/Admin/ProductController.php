<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(12);
        return view('adm.produtos.index', compact('products'));
    }

    public function create()
    {
        return view('adm.produtos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'sku'         => 'nullable|string|max:100|unique:products,sku',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'cost_price'  => 'nullable|numeric|min:0',
            'stock'       => 'nullable|integer|min:0',
            'active'      => 'boolean',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_url'   => 'nullable|url',
        ]);

        $data['slug'] = Str::slug($data['name']).'-'.Str::random(5);

        // upload local (prioritário)
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        return redirect()
            ->route('adm.produtos.index')
            ->with('status', "Produto {$product->name} criado com sucesso!");
    }

    public function edit(Product $produto)
    {
        return view('adm.produtos.edit', ['product' => $produto]);
    }

    public function update(Request $request, Product $produto)
    {
        $data = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'sku'         => 'nullable|string|max:100|unique:products,sku,'.$produto->id,
            'description' => 'nullable|string',
            'price'       => 'sometimes|required|numeric|min:0',
            'cost_price'  => 'nullable|numeric|min:0',
            'stock'       => 'nullable|integer|min:0',
            'active'      => 'boolean',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_url'   => 'nullable|url',
        ]);

        if (isset($data['name'])) {
            // só atualiza se não existir slug
            $data['slug'] = $produto->slug ?: Str::slug($data['name']).'-'.Str::random(5);
        }

        if ($request->hasFile('image')) {
            // remove antiga se existir
            if ($produto->image_path && Storage::disk('public')->exists($produto->image_path)) {
                Storage::disk('public')->delete($produto->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $produto->update($data);

        return redirect()
            ->route('adm.produtos.edit', $produto)
            ->with('status', "Produto atualizado!");
    }

    public function destroy(Product $produto)
    {
        if ($produto->image_path && Storage::disk('public')->exists($produto->image_path)) {
            Storage::disk('public')->delete($produto->image_path);
        }
        $produto->delete();
        return redirect()->route('adm.produtos.index')->with('status', 'Produto removido.');
    }
}
