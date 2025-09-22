<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap gap-2 items-center justify-between sm:gap-3">
            <div class="min-w-0 flex-1">
                <h2 class="truncate font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
                    Novo produto
                </h2>
                <p class="mt-0.5 hidden xs:block text-[10px] sm:text-xs text-gray-500">
                    resources\views\adm\produtos\create.blade.php
                </p>
            </div>

            <div class="w-full xs:w-auto">
                @if(Route::has('adm.produtos.index'))
                    <a href="{{ route('adm.produtos.index') }}"
                       class="w-full xs:w-auto inline-flex items-center justify-center rounded-md border px-3 py-2 text-sm font-medium hover:bg-gray-50">
                        Voltar ao catálogo
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto w-full max-w-6xl px-3 sm:px-4 lg:px-6">
            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 text-red-800 px-4 py-3 border border-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <div
                x-data="{
                    name: '',
                    stock: 0,
                    price: 0,
                    cost: 0,
                    url: '',
                    filePreview: '',
                    get margin() {
                        const p = parseFloat(this.price) || 0;
                        const c = parseFloat(this.cost) || 0;
                        if (p <= 0) return 0;
                        return Math.max(0, ((p - c) / p) * 100).toFixed(2);
                    },
                    previewSrc() {
                        return this.filePreview || this.url || 'https://picsum.photos/seed/placeholder/640/640';
                    },
                    onFileChange(e) {
                        const f = e.target.files?.[0];
                        this.filePreview = f ? URL.createObjectURL(f) : '';
                    }
                }"
                class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
            >
                <div class="p-4 sm:p-5">
                    <form
                        method="POST"
                        action="{{ route('adm.produtos.store') }}"
                        enctype="multipart/form-data"
                        class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5"
                    >
                        @csrf

                        {{-- Coluna esquerda (conteúdo principal) --}}
                        <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
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

                            <div>
                                <x-input-label value="SKU" />
                                <x-text-input
                                    name="sku"
                                    type="text"
                                    class="mt-1 block w-full"
                                    maxlength="100"
                                    placeholder="Ex.: 12584"
                                    autocomplete="off"
                                />
                                <x-input-error :messages="$errors->get('sku')" class="mt-2" />
                            </div>

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

                            <div>
                                <x-input-label value="Margem estimada" />
                                <div class="mt-1 h-10 flex items-center rounded-md border border-gray-300 px-3 text-gray-700 bg-gray-50">
                                    <span class="tabular-nums" x-text="margin + ' %'"></span>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label value="Descrição" />
                                <textarea
                                    name="description"
                                    rows="5"
                                    class="mt-1 block w-full rounded-md border-gray-300 focus:border-gray-400 focus:ring-0"
                                ></textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <label class="flex items-center gap-2">
                                    <input type="hidden" name="active" value="0">
                                    <x-checkbox name="active" checked />
                                    <span class="text-sm text-gray-700">Ativo (aparece na vitrine)</span>
                                </label>
                            </div>
                        </div>

                        {{-- Coluna direita (mídia / ações) --}}
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

                            <div class="mt-4 flex flex-col xs:flex-row items-stretch xs:items-center gap-2">
                                <x-primary-button class="w-full xs:w-auto justify-center">Salvar produto</x-primary-button>
                                <a href="{{ route('adm.produtos.index') }}"
                                   class="w-full xs:w-auto inline-flex justify-center rounded-md border px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    Ver todos
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Dicas de toque para mobile (opcional, não exibido em telas grandes) --}}
            <p class="mt-3 text-xs text-gray-500 sm:hidden">
                Dica: toque nos campos para abrir o teclado numérico/decimal onde aplicável.
            </p>
        </div>
    </div>
</x-app-layout>
