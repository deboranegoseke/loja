<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2 sm:gap-3">
            <div class="min-w-0 flex-1">
                <h2 class="truncate font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
                    Editar produto
                </h2>
                <p class="mt-0.5 text-[10px] sm:text-xs text-gray-500">
                    resources\views\adm\produtos\edit.blade.php
                </p>
            </div>

            <div class="w-full sm:w-auto">
                <a href="{{ route('adm.produtos.index') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center rounded-md border px-3 py-2 text-sm font-medium hover:bg-gray-50">
                    Voltar ao catálogo
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto w-full max-w-6xl px-3 sm:px-4 lg:px-6">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-5">

                    {{-- Mensagens de feedback --}}
                    @if (session('success') || session('status'))
                        <div class="mb-4 rounded-lg bg-green-50 text-green-800 px-4 py-3 border border-green-200">
                            {{ session('success') ?? session('status') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 rounded-lg bg-red-50 text-red-800 px-4 py-3 border border-red-200">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="mb-4 rounded-lg bg-red-50 text-red-800 px-4 py-3 border border-red-200">
                            <ul class="list-disc ms-5 space-y-0.5">
                                @foreach ($errors->all() as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    {{-- /Mensagens de feedback --}}

                    <div
                        x-data="{
                            name: @js(old('name', $product->name)),
                            sku:  @js(old('sku',  $product->sku)),
                            stock: @js((string) old('stock', $product->stock)),
                            price: @js((string) old('price', $product->price)),
                            cost:  @js((string) old('cost_price', $product->cost_price)),
                            url:   @js(old('image_url', $product->image_url)),
                            filePreview: '',
                            get margin() {
                                const p = parseFloat(this.price) || 0;
                                const c = parseFloat(this.cost) || 0;
                                if (p <= 0) return 0;
                                return Math.max(0, ((p - c) / p) * 100).toFixed(2);
                            },
                            previewSrc() {
                                return this.filePreview || this.url || @js($product->cover_url ?? '') || 'https://picsum.photos/seed/placeholder/640/640';
                            },
                            onFileChange(e) {
                                const f = e.target.files?.[0];
                                this.filePreview = f ? URL.createObjectURL(f) : '';
                            }
                        }"
                    >
                        <form
                            method="POST"
                            action="{{ route('adm.produtos.update', $product) }}"
                            enctype="multipart/form-data"
                            class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5"
                        >
                            @csrf @method('PUT')

                            {{-- Coluna esquerda: campos --}}
                            <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Nome --}}
                                <div class="md:col-span-2">
                                    <x-input-label value="Nome do produto" />
                                    <x-text-input
                                        name="name"
                                        x-model="name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                        autocomplete="off"
                                    />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                {{-- SKU --}}
                                <div>
                                    <x-input-label value="SKU (opcional)" />
                                    <x-text-input
                                        name="sku"
                                        x-model="sku"
                                        type="text"
                                        class="mt-1 block w-full"
                                        maxlength="100"
                                        placeholder="Ex.: 1256"
                                        autocomplete="off"
                                    />
                                    <x-input-error :messages="$errors->get('sku')" class="mt-2" />
                                </div>

                                {{-- Estoque --}}
                                <div>
                                    <x-input-label value="Estoque" />
                                    <x-text-input
                                        name="stock"
                                        x-model="stock"
                                        type="number"
                                        min="0"
                                        step="1"
                                        inputmode="numeric"
                                        class="mt-1 block w-full"
                                    />
                                    <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                                </div>

                                {{-- Preço --}}
                                <div>
                                    <x-input-label value="Preço de venda (R$)" />
                                    <x-text-input
                                        name="price"
                                        x-model="price"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        inputmode="decimal"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                                </div>

                                {{-- Custo --}}
                                <div>
                                    <x-input-label value="Custo (R$) — não aparece na vitrine" />
                                    <x-text-input
                                        name="cost_price"
                                        x-model="cost"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        inputmode="decimal"
                                        class="mt-1 block w-full"
                                    />
                                    <x-input-error :messages="$errors->get('cost_price')" class="mt-2" />
                                </div>

                                {{-- Margem estimada --}}
                                <div>
                                    <x-input-label value="Margem estimada" />
                                    <div class="mt-1 h-10 flex items-center rounded-md border border-gray-300 px-3 text-gray-700 bg-gray-50">
                                        <span class="tabular-nums" x-text="margin + ' %'"></span>
                                    </div>
                                </div>

                                {{-- Descrição --}}
                                <div class="md:col-span-2">
                                    <x-input-label value="Descrição" />
                                    <textarea
                                        name="description"
                                        rows="5"
                                        class="mt-1 block w-full rounded-md border-gray-300 focus:border-gray-400 focus:ring-0"
                                    >{{ old('description', $product->description) }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                                {{-- Status (Ativo / Inativo) --}}
                                <div class="md:col-span-2">
                                    <x-input-label value="Status do produto" />
                                    <div class="mt-2 flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6">
                                        <label class="inline-flex items-center gap-2">
                                            <input type="radio" name="active" value="1"
                                                   @checked((string) old('active', (int) $product->active) === '1')
                                                   class="rounded border-gray-300">
                                            <span class="text-sm text-gray-700">Ativo (aparece na vitrine)</span>
                                        </label>
                                        <label class="inline-flex items-center gap-2">
                                            <input type="radio" name="active" value="0"
                                                   @checked((string) old('active', (int) $product->active) === '0')
                                                   class="rounded border-gray-300">
                                            <span class="text-sm text-gray-700">Inativo</span>
                                        </label>
                                    </div>
                                    <x-input-error :messages="$errors->get('active')" class="mt-2" />
                                </div>
                            </div>

                            {{-- Coluna direita: imagem/preview --}}
                            <div class="lg:pl-2">
                                <div class="rounded-lg border border-dashed border-gray-300 p-3 lg:sticky lg:top-4">
                                    <div class="aspect-square w-full overflow-hidden rounded-lg bg-gray-100">
                                        <img
                                            :src="previewSrc()"
                                            alt="Pré-visualização"
                                            class="h-full w-full object-cover"
                                            loading="lazy"
                                        >
                                    </div>

                                    <div class="mt-3">
                                        <x-input-label value="Upload de imagem" />
                                        <input
                                            type="file"
                                            name="image"
                                            accept="image/*"
                                            @change="onFileChange"
                                            class="mt-1 block w-full text-sm file:mr-3 file:rounded-md file:border file:border-gray-300 file:bg-white file:px-3 file:py-2 file:text-sm file:font-medium hover:file:bg-gray-50"
                                        >
                                        <x-input-error :messages="$errors->get('image')" class="mt-2" />
                                    </div>

                                    <div class="mt-3">
                                        <x-input-label value="ou URL da imagem" />
                                        <x-text-input
                                            name="image_url"
                                            x-model="url"
                                            type="url"
                                            class="mt-1 block w-full"
                                            placeholder="https://..."
                                            autocomplete="off"
                                        />
                                        <x-input-error :messages="$errors->get('image_url')" class="mt-2" />
                                    </div>
                                </div>

                                <div class="mt-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                                    <x-primary-button class="w-full sm:w-auto justify-center">Salvar alterações</x-primary-button>
                                    <a href="{{ route('adm.produtos.index') }}"
                                       class="w-full sm:w-auto inline-flex justify-center rounded-md border px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        Cancelar
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                </div> {{-- /p --}}
            </div> {{-- /card --}}
        </div> {{-- /container --}}
    </div>
</x-app-layout>
