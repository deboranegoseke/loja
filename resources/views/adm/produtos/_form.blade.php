@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label value="Nome" />
        <x-text-input name="name" type="text" class="mt-1 block w-full"
                      :value="old('name', $product->name ?? '')" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label value="SKU (opcional)" />
        <x-text-input name="sku" type="text" class="mt-1 block w-full"
                      :value="old('sku', $product->sku ?? '')" />
        <x-input-error :messages="$errors->get('sku')" class="mt-2" />
    </div>

    <div>
        <x-input-label value="Preço de venda (R$)" />
        <x-text-input name="price" type="number" step="0.01" min="0" class="mt-1 block w-full"
                      :value="old('price', $product->price ?? '')" required />
        <x-input-error :messages="$errors->get('price')" class="mt-2" />
    </div>

    <div>
        <x-input-label value="Custo (R$) — não aparece na vitrine" />
        <x-text-input name="cost_price" type="number" step="0.01" min="0" class="mt-1 block w-full"
                      :value="old('cost_price', $product->cost_price ?? '')" />
        <x-input-error :messages="$errors->get('cost_price')" class="mt-2" />
    </div>

    <div>
        <x-input-label value="Estoque" />
        <x-text-input name="stock" type="number" step="1" min="0" class="mt-1 block w-full"
                      :value="old('stock', $product->stock ?? 0)" />
        <x-input-error :messages="$errors->get('stock')" class="mt-2" />
    </div>

    <div class="flex items-center gap-3">
        <input type="hidden" name="active" value="0">
        <x-checkbox name="active" :checked="old('active', ($product->active ?? true))" />
        <span class="text-sm">Ativo (aparece na vitrine)</span>
    </div>

    <div class="md:col-span-2">
        <x-input-label value="Descrição" />
        <textarea name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300">{{ old('description', $product->description ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div>
        <x-input-label value="Imagem (upload)" />
        <input type="file" name="image" accept="image/*" class="mt-1 block w-full">
        <x-input-error :messages="$errors->get('image')" class="mt-2" />
    </div>

    <div>
        <x-input-label value="OU URL da imagem (opcional)" />
        <x-text-input name="image_url" type="url" class="mt-1 block w-full"
                      :value="old('image_url', $product->image_url ?? '')" />
        <x-input-error :messages="$errors->get('image_url')" class="mt-2" />
    </div>

    @isset($product)
        <div class="md:col-span-2">
            <x-input-label value="Pré-visualização" />
            <div class="mt-2 flex items-center gap-4">
                <img src="{{ $product->cover_url }}" class="h-28 w-28 object-cover rounded border" alt="">
                <div class="text-sm text-gray-600">
                    @if(!is_null($product->margin_percent))
                        Margem estimada: <strong>{{ number_format($product->margin_percent, 2, ',', '.') }}%</strong>
                    @endif
                </div>
            </div>
        </div>
    @endisset
</div>

<div class="mt-6 flex items-center gap-3">
    <x-primary-button>Salvar</x-primary-button>
    <a href="{{ route('adm.produtos.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Voltar</a>
</div>
