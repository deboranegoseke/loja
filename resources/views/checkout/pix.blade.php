<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2 sm:gap-3">
            <div class="min-w-0">
                <h2 class="truncate font-semibold text-lg sm:text-xl text-gray-800 leading-tight">Pagamento via Pix</h2>
                <h6 class="mt-0.5 text-[10px] sm:text-xs text-gray-500">resources\views\checkout\pix.blade.php</h6>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto w-full max-w-4xl px-3 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-green-50 text-green-800 px-4 py-3 border border-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-5">
                    <p class="text-gray-700">
                        Pedido #{{ $order->id }} — Total:
                        <strong>R$ {{ number_format($order->total, 2, ',', '.') }}</strong>
                    </p>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 items-start">
                        {{-- Coluna QR --}}
                        <div class="order-1 sm:order-none">
                            <div class="rounded-lg border border-gray-200 p-4 lg:sticky lg:top-4">
                                <div class="flex flex-col items-center">
                                    @if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class))
                                        {{-- Tenta usar SVG (nítido e responsivo). Ajuste o size conforme necessário --}}
                                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(240)->generate($payload) !!}
                                    @else
                                        <div class="rounded-lg border p-6 text-center text-sm text-gray-500">
                                            Instale <code>simplesoftwareio/simple-qrcode</code> para exibir o QR.<br>
                                            Use o “copia e cola” ao lado.
                                        </div>
                                    @endif
                                    <div class="mt-3 text-xs text-gray-500 text-center">
                                        Escaneie no app do seu banco
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Coluna Copia e Cola / Ações --}}
                        <div>
                            <label for="pix-code" class="text-sm font-medium text-gray-700">Pix copia e cola</label>
                            <textarea
                                id="pix-code"
                                readonly
                                rows="8"
                                class="mt-2 w-full rounded-md border-gray-300 focus:border-gray-400 focus:ring-0 text-sm"
                            >{{ $payload }}</textarea>

                            {{-- Botão copiar com feedback acessível --}}
                            <div class="mt-2 flex flex-col xs:flex-row gap-2">
                                <button
                                    type="button"
                                    id="copy-btn"
                                    data-clipboard-text="{{ $payload }}"
                                    class="w-full xs:w-auto inline-flex items-center justify-center rounded-md bg-pink-600 px-4 py-2 text-sm font-semibold text-white hover:bg-pink-700"
                                >
                                    Copiar código
                                </button>

                                <span id="copy-feedback" class="hidden text-sm text-green-700 xs:self-center" role="status" aria-live="polite">
                                    Código copiado!
                                </span>
                            </div>

                            {{-- Confirmação simulada --}}
                            <div class="mt-6 text-sm text-gray-600">
                                Após pagar, clique abaixo (simulação de confirmação):
                            </div>
                            <form method="POST" action="{{ route('pix.confirm', $order) }}" class="mt-2">
                                @csrf
                                <x-primary-button class="bg-green-600 hover:bg-green-700">
                                    Já paguei
                                </x-primary-button>
                            </form>

                            @if ($order->status === 'paid')
                                <div class="mt-4 rounded-lg bg-green-50 text-green-800 px-4 py-3 border border-green-200">
                                    Pagamento confirmado! ✔
                                </div>
                            @endif

                            <div class="mt-6">
                                <a href="{{ url('/') }}" class="text-sm text-gray-600 hover:text-gray-900">Voltar à vitrine</a>
                            </div>
                        </div>
                    </div>
                </div> {{-- /p --}}
            </div> {{-- /card --}}
        </div>
    </div>

    {{-- Script de cópia (sem depender de backticks no onclick) --}}
    <script>
        (function () {
            const btn = document.getElementById('copy-btn');
            const fb  = document.getElementById('copy-feedback');
            if (!btn) return;

            btn.addEventListener('click', async () => {
                const text = btn.getAttribute('data-clipboard-text') || '';
                try {
                    if (navigator.clipboard && window.isSecureContext) {
                        await navigator.clipboard.writeText(text);
                    } else {
                        // Fallback: cria um textarea temporário
                        const ta = document.createElement('textarea');
                        ta.value = text;
                        ta.style.position = 'fixed';
                        ta.style.left = '-9999px';
                        document.body.appendChild(ta);
                        ta.focus();
                        ta.select();
                        document.execCommand('copy');
                        document.body.removeChild(ta);
                    }
                    if (fb) {
                        fb.classList.remove('hidden');
                        fb.textContent = 'Código copiado!';
                        setTimeout(() => fb.classList.add('hidden'), 2500);
                    }
                } catch (e) {
                    if (fb) {
                        fb.classList.remove('hidden');
                        fb.textContent = 'Não foi possível copiar. Selecione o texto e copie manualmente.';
                        setTimeout(() => fb.classList.add('hidden'), 3000);
                    }
                }
            }, { passive: true });
        })();
    </script>
</x-app-layout>
