<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Carrinho</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-green-50 text-green-800 px-4 py-3 border border-green-200">
                    {{ session('status') }}
                </div>
            @endif

            @if(empty($cart['items']))
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-600">
                    Seu carrinho está vazio. <a href="{{ url('/') }}" class="text-indigo-600">Voltar à vitrine</a>.
                </div>
            @else
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="space-y-4">
                    @foreach($cart['items'] as $it)
                        <div class="flex items-center gap-4 border-b pb-4">
                            <img src="{{ $it['image'] }}" class="h-16 w-16 rounded object-cover" alt="">
                            <div class="flex-1">
                                <div class="font-semibold">{{ $it['name'] }}</div>
                                <div class="text-sm text-gray-500">R$ {{ number_format($it['price'],2,',','.') }}</div>
                            </div>

                            <form method="POST" action="{{ route('cart.update', $it['id']) }}" class="flex items-center gap-2">
                                @csrf @method('PATCH')
                                <input type="number" name="qty" min="0" value="{{ $it['qty'] }}"
                                       class="w-20 rounded-md border-gray-300">
                                <x-primary-button>Atualizar</x-primary-button>
                            </form>

                            <form method="POST" action="{{ route('cart.remove', $it['id']) }}">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 text-sm">Remover</button>
                            </form>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <form method="POST" action="{{ route('cart.clear') }}">@csrf
                        <button class="text-sm text-gray-600 hover:text-gray-900">Limpar carrinho</button>
                    </form>
                    <div class="text-right">
                        <div class="text-sm text-gray-500">Subtotal</div>
                        <div class="text-2xl font-bold">R$ {{ number_format($cart['subtotal'],2,',','.') }}</div>
                    </div>
                </div>

                @guest
                    <div class="mt-6">
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center rounded-md bg-pink-600 px-5 py-3 text-sm font-semibold text-white hover:bg-pink-700">
                           Entrar para finalizar
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                               class="ml-3 inline-flex items-center rounded-md bg-white px-5 py-3 text-sm font-semibold text-gray-900 ring-1 ring-gray-200 hover:bg-gray-50">
                               Criar conta
                            </a>
                        @endif
                    </div>
                @else
                    <form method="POST" action="{{ route('checkout.store') }}" class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @csrf
                        <input type="text"  name="name"  value="{{ old('name', auth()->user()->name) }}"  class="rounded-md border-gray-300 sm:col-span-1">
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="rounded-md border-gray-300 sm:col-span-1">
                        <x-primary-button class="sm:col-span-1 justify-center bg-pink-600 hover:bg-pink-700">Pagar com Pix</x-primary-button>
                    </form>
                @endguest
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
