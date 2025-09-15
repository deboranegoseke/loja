<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pagamento via Pix</h2>
        <h6>resources\views\checkout\pix.blade.php</h6>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-green-50 text-green-800 px-4 py-3 border border-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-gray-600">Pedido #{{ $order->id }} — Total:
                    <strong>R$ {{ number_format($order->total,2,',','.') }}</strong>
                </p>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-6 items-start">
                    <div class="flex flex-col items-center">
                        {{-- QR CODE (se instalou o pacote) --}}
                        @if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class))
                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(220)->generate($payload) !!}
                        @else
                            <div class="rounded-lg border p-6 text-center text-sm text-gray-500">
                                Instale <code>simplesoftwareio/simple-qrcode</code> para exibir o QR.<br>
                                Use o “copia e cola” ao lado.
                            </div>
                        @endif
                        <div class="mt-3 text-xs text-gray-500">Escaneie no app do seu banco</div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Pix copia e cola</label>
                        <textarea readonly rows="8" class="mt-2 w-full rounded-md border-gray-300">{{ $payload }}</textarea>
                        <button onclick="navigator.clipboard.writeText(`{{ $payload }}`)"
                                class="mt-2 inline-flex items-center rounded-md bg-pink-600 px-3 py-2 text-sm font-semibold text-white hover:bg-pink-700">
                            Copiar código
                        </button>

                        <div class="mt-6 text-sm text-gray-600">
                            Após pagar, clique abaixo (simulação de confirmação):
                        </div>
                        <form method="POST" action="{{ route('pix.confirm', $order) }}" class="mt-2">
                            @csrf
                            <x-primary-button class="bg-green-600 hover:bg-green-700">Já paguei</x-primary-button>
                        </form>

                        @if($order->status === 'paid')
                            <div class="mt-4 rounded-lg bg-green-50 text-green-800 px-4 py-3 border border-green-200">
                                Pagamento confirmado! ✔
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ url('/') }}" class="text-sm text-gray-600 hover:text-gray-900">Voltar à vitrine</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
