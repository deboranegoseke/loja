<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q      = $request->string('q')->toString();
        $active = $request->input('active'); // '1', '0' ou null/''
        $stock  = $request->input('stock');  // 'in', 'out' ou null/''

        $products = Product::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qb) use ($q) {
                    $qb->where('name', 'like', "%{$q}%")
                       ->orWhere('sku', 'like', "%{$q}%");
                });
            })
            ->when($active !== null && $active !== '', function ($query) use ($active) {
                $query->where('active', $active === '1' ? 1 : 0);
            })
            ->when($stock === 'in', function ($query) {
                $query->where('stock', '>', 0);
            })
            ->when($stock === 'out', function ($query) {
                $query->where(function ($q2) {
                    $q2->whereNull('stock')->orWhere('stock', '<=', 0);
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('adm.produtos.index', compact('products', 'q', 'active', 'stock'));
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
            'active'      => 'sometimes|boolean',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_url'   => 'nullable|url',
        ]);

        $data['active'] = $request->boolean('active');
        $data['slug']   = Str::slug($data['name']).'-'.Str::random(5);

        try {
            if ($request->hasFile('image')) {
                $data['image_path'] = $request->file('image')->store('products', 'public');
            }

            $product = Product::create($data);

            return redirect()
                ->route('adm.produtos.edit', $product)
                ->with('success', "Produto {$product->name} criado com sucesso!");
        } catch (\Throwable $e) {
            Log::error('Erro ao criar produto', ['error' => $e->getMessage()]);
            return back()
                ->with('error', 'Não foi possível criar o produto. Tente novamente.')
                ->withInput();
        }
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
            'active'      => 'sometimes|boolean',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_url'   => 'nullable|url',
        ]);

        if (array_key_exists('active', $data)) {
            $data['active'] = $request->boolean('active');
        }

        if (isset($data['name'])) {
            $data['slug'] = $produto->slug ?: Str::slug($data['name']).'-'.Str::random(5);
        }

        try {
            if ($request->hasFile('image')) {
                if ($produto->image_path && Storage::disk('public')->exists($produto->image_path)) {
                    Storage::disk('public')->delete($produto->image_path);
                }
                $data['image_path'] = $request->file('image')->store('products', 'public');
            }

            $produto->update($data);

            return redirect()
                ->route('adm.produtos.edit', $produto)
                ->with('success', 'Produto atualizado com sucesso!');
        } catch (\Throwable $e) {
            Log::error('Erro ao atualizar produto', ['product_id' => $produto->id, 'error' => $e->getMessage()]);
            return back()
                ->with('error', 'Não foi possível atualizar o produto. Tente novamente.')
                ->withInput();
        }
    }

    public function destroy(Product $produto)
    {
        try {
            if ($produto->image_path && Storage::disk('public')->exists($produto->image_path)) {
                Storage::disk('public')->delete($produto->image_path);
            }
            $produto->delete();

            return redirect()
                ->route('adm.produtos.index')
                ->with('success', 'Produto removido com sucesso.');
        } catch (\Throwable $e) {
            Log::error('Erro ao remover produto', ['product_id' => $produto->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Não foi possível remover o produto.');
        }
    }
}
