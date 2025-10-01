{{-- resources/views/cart/index.blade.php --}}
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
                            <div class="space-y-3" id="cart-items">
                                @foreach($cart['items'] as $it)
                                    @php
                                        $itemId   = $it['id'];
                                        $qty      = (int)($it['qty'] ?? 0);
                                        $price    = (float)($it['price'] ?? 0);
                                        $itemTotal = $qty * $price;
                                    @endphp
                                    <div class="rounded-lg border border-gray-200 p-3 cart-row"
                                         data-item-id="{{ $itemId }}"
                                         data-price="{{ number_format($price, 2, '.', '') }}">
                                        <div class="flex items-start gap-3">
                                            <img
                                                src="{{ $it['image'] ?? 'https://picsum.photos/seed/placeholder/96/96' }}"
                                                class="h-16 w-16 sm:h-20 sm:w-20 rounded object-cover border"
                                                alt="{{ $it['name'] ?? 'Produto' }}"
                                                loading="lazy"
                                            >
                                            <div class="min-w-0 flex-1">
                                                <div class="font-semibold text-gray-900 truncate">{{ $it['name'] }}</div>
                                                <div class="mt-0.5 text-sm text-gray-500">
                                                    R$ {{ number_format($price, 2, ',', '.') }}
                                                </div>

                                                {{-- Controles: quantidade (+/-) / remover --}}
                                                <div class="mt-3 flex flex-col gap-2 w-auto max-w-fit">
                                                    {{-- Grupo super compacto (NÃO expandir) --}}
                                                    <div class="inline-flex items-center gap-1 w-auto max-w-fit shrink-0">
                                                        {{-- Botão diminuir --}}
                                                        <button type="button"
                                                                class="qty-btn inline-flex items-center justify-center border border-gray-400 rounded-sm w-8 h-8 sm:w-9 sm:h-9 text-sm leading-none text-gray-800 hover:bg-gray-50"
                                                                data-delta="-1" aria-label="Diminuir">
                                                            &lt;
                                                        </button>

                                                        {{-- Campo quantidade (3–3.5ch) --}}
                                                        <input type="text"
                                                               class="qty-input border border-gray-400 rounded-sm w-[3.5ch] h-8 sm:h-9 flex-none text-center text-sm font-mono tabular-nums outline-none"
                                                               value="{{ str_pad($qty, 2, '0', STR_PAD_LEFT) }}"
                                                               inputmode="numeric" aria-label="Quantidade" readonly>

                                                        {{-- Botão aumentar --}}
                                                        <button type="button"
                                                                class="qty-btn inline-flex items-center justify-center border border-gray-400 rounded-sm w-8 h-8 sm:w-9 sm:h-9 text-sm leading-none text-gray-800 hover:bg-gray-50"
                                                                data-delta="1" aria-label="Aumentar">
                                                            &gt;
                                                        </button>
                                                    </div>

                                                    {{-- Remover (única ação de exclusão) --}}
                                                    <form method="POST" action="{{ route('cart.remove', $itemId) }}" class="remove-form">
                                                        @csrf @method('DELETE')
                                                        <button
                                                            class="inline-flex items-center gap-2 rounded-md border border-red-300 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-400/30"
                                                            onclick="return confirm('Remover este produto?')"
                                                        >
                                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 100 2h.293l.853 10.235A2 2 0 007.139 18h5.722a2 2 0 001.993-1.765L15.707 6H16a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zm-1 6a1 1 0 112 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 112 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd"/>
                                                            </svg>
                                                            Remover
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Subtotal do item (útil no mobile) --}}
                                        <div class="mt-3 flex items-center justify-between text-sm text-gray-600 sm:hidden">
                                            <span>Total do item</span>
                                            <span class="font-semibold text-gray-900 item-total">
                                                R$ {{ number_format($itemTotal, 2, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Ações de lista --}}
                            <div class="mt-4 flex flex-col xs:flex-row xs:items-center gap-2">
                                <a href="{{ url('/') }}"
                                   class="w-full xs:w-auto inline-flex justify-center rounded-md border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700 hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-200">
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
                                        <dd class="font-semibold" id="cart-subtotal">
                                            R$ {{ number_format($cart['subtotal'], 2, ',', '.') }}
                                        </dd>
                                    </div>
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

    {{-- Atualização automática de quantidade e totais --}}
    <script>
        (function () {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            function toMoney(n) {
                try {
                    return 'R$ ' + (Number(n).toFixed(2)).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                } catch (_) { return 'R$ 0,00'; }
            }

            function recalcSubtotal() {
                let sum = 0;
                document.querySelectorAll('.cart-row').forEach(row => {
                    const price = Number(row.dataset.price || 0);
                    const qty = Number(row.querySelector('.qty-input')?.value || 0);
                    sum += price * qty;
                });
                const el = document.getElementById('cart-subtotal');
                if (el) el.textContent = toMoney(sum);
            }

            async function updateQty(row, newQty) {
                const itemId = row.dataset.itemId;
                if (!itemId) return;

                newQty = Math.max(0, Number(newQty || 0)); // mínimo 0

                // Preenche com 2 dígitos para ficar como no mock: 01, 02, 25...
                const display = String(newQty).padStart(2, '0');

                const input = row.querySelector('.qty-input');
                if (input) input.value = display;

                const price = Number(row.dataset.price || 0);
                const itemTotalEl = row.querySelector('.item-total');
                if (itemTotalEl) itemTotalEl.textContent = toMoney(price * newQty);

                recalcSubtotal();

                if (newQty === 0) {
                    const form = row.querySelector('.remove-form');
                    if (form) form.submit();
                    return;
                }

                try {
                    await fetch(`{{ url('cart') }}/${itemId}`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ qty: newQty })
                    });
                } catch (e) {
                    console.error(e);
                }
            }

            document.addEventListener('click', function (ev) {
                const btn = ev.target.closest('.qty-btn');
                if (!btn) return;

                const row = ev.target.closest('.cart-row');
                if (!row) return;

                const input = row.querySelector('.qty-input');
                const current = Number((input?.value || '0').replace(/\D/g, '') || 0);
                const delta = Number(btn.dataset.delta || 0);

                updateQty(row, current + delta);
            }, false);
        })();
    </script>
</x-app-layout>
