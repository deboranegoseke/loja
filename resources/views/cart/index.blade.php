<x-app-layout> 
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2 sm:gap-3">
            <div class="min-w-0">
                <h2 class="truncate font-semibold text-lg sm:text-xl text-gray-800 leading-tight">Carrinho</h2>
                <h6 class="mt-0.5 text-[10px] sm:text-xs text-gray-500">resources\views\cart\index.blade.php</h6>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto w-full max-w-6xl px-3 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-green-50 text-green-800 px-4 py-3 border border-green-200">
                    {{ session('status') }}
                </div>
            @endif

            @if(empty($cart['items']))
                <div class="bg-white shadow-sm sm:rounded-lg p-5 text-gray-600">
                    Seu carrinho está vazio.
                    <a href="{{ url('/') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">Voltar à vitrine</a>.
                </div>
            @else
                <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-5">
                    {{-- Grade principal: lista (lg:col-span-2) + resumo (lg:col-span-1) --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5">
                        {{-- Lista de itens --}}
                        <div class="lg:col-span-2">
                            <div class="space-y-3">
                                @foreach($cart['items'] as $it)
                                    <div class="rounded-lg border border-gray-200 p-3">
                                        <div class="flex items-start gap-3">
                                            <img
                                                src="{{ $it['image'] ?? 'https://picsum.photos/seed/placeholder/96/96' }}"
                                                class="h-16 w-16 sm:h-20 sm:w-20 rounded object-cover border"
                                                alt=""
                                                loading="lazy"
                                            >
                                            <div class="min-w-0 flex-1">
                                                <div class="font-semibold text-gray-900 truncate">{{ $it['name'] }}</div>
                                                <div class="mt-0.5 text-sm text-gray-500">
                                                    R$ {{ number_format($it['price'], 2, ',', '.') }}
                                                </div>

                                                {{-- Controles: quantidade / atualizar / remover --}}
                                                <div class="mt-3 flex flex-col xs:flex-row xs:items-center gap-2">
                                                    <form method="POST" action="{{ route('cart.update', $it['id']) }}" class="flex items-center gap-2">
                                                        @csrf @method('PATCH')
                                                        <label class="sr-only" for="qty-{{ $it['id'] }}">Quantidade</label>
                                                        <input
                                                            id="qty-{{ $it['id'] }}"
                                                            type="number"
                                                            name="qty"
                                                            min="0"
                                                            step="1"
                                                            inputmode="numeric"
                                                            value="{{ $it['qty'] }}"
                                                            class="w-24 rounded-md border-gray-300"
                                                        />
                                                        <x-primary-button class="justify-center">
                                                            Atualizar
                                                        </x-primary-button>
                                                    </form>

                                                    <form method="POST" action="{{ route('cart.remove', $it['id']) }}" class="xs:ml-2">
                                                        @csrf @method('DELETE')
                                                        <button
                                                            class="inline-flex items-center rounded-md border px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50"
                                                            onclick="return confirm('Remover este produto?')"
                                                        >
                                                            Remover
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Subtotal do item (opcional, útil no mobile) --}}
                                        <div class="mt-3 flex items-center justify-between text-sm text-gray-600 sm:hidden">
                                            <span>Total do item</span>
                                            @php
                                                $itemTotal = ($it['qty'] ?? 0) * ($it['price'] ?? 0);
                                            @endphp
                                            <span class="font-semibold text-gray-900">R$ {{ number_format($itemTotal, 2, ',', '.') }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Ações de lista --}}
                            <div class="mt-4 flex flex-col xs:flex-row xs:items-center gap-2">
                                <form method="POST" action="{{ route('cart.clear') }}">
                                    @csrf
                                    <button class="w-full xs:w-auto inline-flex justify-center rounded-md border px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        Limpar carrinho
                                    </button>
                                </form>

                                <a href="{{ url('/') }}"
                                   class="w-full xs:w-auto inline-flex justify-center rounded-md border px-4 py-2 text-sm font-medium hover:bg-gray-50">
                                    Continuar comprando
                                </a>
                            </div>
                        </div>

                        {{-- Resumo / Checkout --}}
                        <aside class="lg:pl-2">
                            <div class="rounded-lg border border-gray-200 p-4 lg:sticky lg:top-4">
                                <h3 class="text-base font-semibold text-gray-900">Resumo</h3>

                                <dl class="mt-3 space-y-1 text-sm">
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600">Subtotal</dt>
                                        <dd class="font-semibold">R$ {{ number_format($cart['subtotal'], 2, ',', '.') }}</dd>
                                    </div>
                                    {{-- Campos adicionais (frete/cupom) podem entrar aqui futuramente --}}
                                </dl>

                                @guest
                                    <div class="mt-4 flex flex-col gap-2">
                                        <a href="{{ route('login') }}"
                                           class="w-full inline-flex items-center justify-center rounded-md bg-pink-600 px-5 py-3 text-sm font-semibold text-white hover:bg-pink-700">
                                           Entrar para finalizar
                                        </a>

                                        @if (Route::has('register'))
                                            <a href="{{ route('register') }}"
                                               class="w-full inline-flex items-center justify-center rounded-md bg-white px-5 py-3 text-sm font-semibold text-gray-900 ring-1 ring-gray-200 hover:bg-gray-50">
                                               Criar conta
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <div class="mt-4">
                                        <form method="POST" action="{{ route('checkout.store') }}">
                                            @csrf
                                            <x-primary-button class="w-full justify-center bg-pink-600 hover:bg-pink-700">
                                                Pagar com Pix
                                            </x-primary-button>
                                        </form>
                                    </div>
                                @endguest
                            </div>
                        </aside>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
