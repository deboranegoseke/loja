{{-- resources/views/customer/sac/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Suporte — Pedido #{{ $order->id }}
                </h2>
                <h6 class="text-gray-400 text-sm mt-1">resources/views/customer/sac/create.blade.php</h6>
            </div>
            <a href="{{ route('cliente.pedidos.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900 transition">
               ← Meus pedidos
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md sm:rounded-lg p-6">
                <form method="POST" action="{{ route('cliente.sac.store') }}" class="grid gap-6">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Mensagem</label>
                        <textarea id="message" name="message" rows="6" placeholder="Descreva seu problema ou dúvida..."
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition" required>{{ old('message') }}</textarea>
                        @error('message') 
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p> 
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button class="bg-pink-600 hover:bg-pink-700 shadow-sm transition">
                            Abrir chamado
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
