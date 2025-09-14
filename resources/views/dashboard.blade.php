<x-app-layout>
    <x-slot name="header">
        @php($user = auth()->user())
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>

            {{-- Atalhos (chips) --}}
            <nav class="flex flex-wrap items-center gap-2">
                <a href="{{ url('/') }}" class="px-3 py-1.5 rounded-full text-sm bg-gray-100 hover:bg-gray-200">Vitrine</a>

                @if($user->hasRole(['cliente','adm','gerente']) && Route::has('cliente.pedidos.index'))
                    <a href="{{ route('cliente.pedidos.index') }}" class="px-3 py-1.5 rounded-full text-sm bg-gray-100 hover:bg-gray-200">Meus Pedidos</a>
                @endif

                @if($user->hasRole(['cliente','adm','gerente']) && Route::has('cliente.sac.index'))
                    <a href="{{ route('cliente.sac.index') }}" class="px-3 py-1.5 rounded-full text-sm bg-gray-100 hover:bg-gray-200">SAC</a>
                @endif

                @if($user->hasRole(['adm','gerente']) && Route::has('adm.produtos.index'))
                    <a href="{{ route('adm.produtos.index') }}" class="px-3 py-1.5 rounded-full text-sm bg-gray-100 hover:bg-gray-200">Catálogo</a>
                @endif

                @if($user->hasRole('gerente') && Route::has('gerente.usuarios.index'))
                    <a href="{{ route('gerente.usuarios.index') }}" class="px-3 py-1.5 rounded-full text-sm bg-gray-100 hover:bg-gray-200">Usuários</a>
                @endif
            </nav>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensagens --}}
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-green-50 text-green-800 px-4 py-3 border border-green-200">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 text-red-800 px-4 py-3 border border-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- CARD DO FORMULÁRIO: só para ADM/GERENTE --}}
            @if($user->hasRole(['adm','gerente']))
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
                            return this.filePreview || this.url || 'https://picsum.photos/seed/placeholder/240/240';
                        },
                        onFileChange(e) {
                            const f = e.target.files?.[0];
                            this.filePreview = f ? URL.createObjectURL(f) : '';
                        }
                    }"
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">Cadastro rápido de produto</h3>
                            <a href="{{ route('adm.produtos.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Gerenciar produtos</a>
                        </div>

                        <form method="POST" action="{{ route('adm.produtos.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            @csrf

                            {{-- Coluna esquerda: campos --}}
                            <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <x-input-label value="Nome do produto" />
                                    <x-text-input name="name" x-model="name" type="text" class="mt-1 block w-full" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label value="Estoque" />
                                    <x-text-input name="stock" x-model="stock" type="number" min="0" step="1" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label value="Preço de venda (R$)" />
                                    <x-text-input name="price" x-model="price" type="number" min="0" step="0.01" class="mt-1 block w-full" required />
                                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label value="Custo (R$) — não aparece na vitrine" />
                                    <x-text-input name="cost_price" x-model="cost" type="number" min="0" step="0.01" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('cost_price')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label value="Margem estimada" />
                                    <div class="mt-1 h-10 flex items-center rounded-md border border-gray-300 px-3 text-gray-700">
                                        <span x-text="margin + ' %'"></span>
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <x-input-label value="Descrição" />
                                    <textarea name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300"></textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                                <div class="md:col-span-2 flex items-center gap-3">
                                    <input type="hidden" name="active" value="0">
                                    <x-checkbox name="active" checked />
                                    <span class="text-sm text-gray-700">Ativo (aparece na vitrine)</span>
                                </div>
                            </div>

                            {{-- Coluna direita: imagem/preview --}}
                            <div>
                                <div class="rounded-lg border border-dashed border-gray-300 p-3">
                                    <div class="aspect-square w-full overflow-hidden rounded-lg bg-gray-100">
                                        <img :src="previewSrc()" alt="Pré-visualização" class="h-full w-full object-cover">
                                    </div>

                                    <div class="mt-3">
                                        <x-input-label value="Upload de imagem" />
                                        <input type="file" name="image" accept="image/*" @change="onFileChange" class="mt-1 block w-full">
                                        <x-input-error :messages="$errors->get('image')" class="mt-2" />
                                    </div>

                                    <div class="mt-3">
                                        <x-input-label value="ou URL da imagem" />
                                        <x-text-input name="image_url" x-model="url" type="url" class="mt-1 block w-full" placeholder="https://..." />
                                        <x-input-error :messages="$errors->get('image_url')" class="mt-2" />
                                    </div>
                                </div>

                                <div class="mt-4 flex items-center gap-3">
                                    <x-primary-button>Salvar produto</x-primary-button>
                                    <a href="{{ route('adm.produtos.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Ver todos</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                {{-- Para clientes: mensagem simples + atalhos --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        {{ __("You're logged in!") }}
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @if(Route::has('cliente.pedidos.index'))
                        <a href="{{ route('cliente.pedidos.index') }}" class="block p-4 rounded-xl border border-gray-200 hover:shadow-sm">
                            <div class="text-sm text-gray-500">Área do Cliente</div>
                            <div class="font-semibold">Meus Pedidos</div>
                        </a>
                    @endif
                    @if(Route::has('cliente.sac.index'))
                        <a href="{{ route('cliente.sac.index') }}" class="block p-4 rounded-xl border border-gray-200 hover:shadow-sm">
                            <div class="text-sm text-gray-500">Suporte</div>
                            <div class="font-semibold">SAC</div>
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
