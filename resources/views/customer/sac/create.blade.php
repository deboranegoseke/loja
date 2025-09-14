{{-- resources/views/customer/sac/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                SAC — Pedido #{{ $order->id }}
            </h2>
            <a href="{{ route('cliente.pedidos.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Meus pedidos</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('cliente.sac.store') }}" class="grid gap-4">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mensagem</label>
                        <textarea name="message" rows="6" class="w-full rounded-md border-gray-300" required>{{ old('message') }}</textarea>
                        @error('message') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end">
                        <x-primary-button class="bg-pink-600 hover:bg-pink-700">Abrir chamado</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
