{{-- resources/views/customer/sac/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="min-w-0">
                <h2 class="truncate font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
                    Suporte — Pedido #{{ $order->id }}
                </h2>
                <h6 class="mt-0.5 text-[10px] sm:text-xs text-gray-400">
                    resources/views/customer/sac/create.blade.php
                </h6>
            </div>

            <div class="w-full sm:w-auto">
                <a href="{{ route('cliente.pedidos.index') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center text-sm rounded-md border px-3 py-2 text-gray-700 hover:bg-gray-50 transition">
                   ← Meus pedidos
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto w-full max-w-3xl px-4 sm:px-6 lg:px-8">
            {{-- Mensagens flash --}}
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-green-50 text-green-800 px-4 py-3 border border-green-200">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-lg bg-red-50 text-red-800 px-4 py-3 border border-red-200">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <form
                        method="POST"
                        action="{{ route('cliente.sac.store') }}"
                        class="grid gap-4 sm:gap-6"
                        x-data="{ msg: @js(old('message','')), max: 2000 }"
                    >
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">

                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Mensagem</label>
                            <textarea
                                id="message"
                                name="message"
                                rows="6"
                                placeholder="Descreva seu problema ou dúvida com o máximo de detalhes possíveis (nº do item, fotos, prazos, etc.)"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition text-sm"
                                x-model="msg"
                                maxlength="2000"
                                required
                            >{{ old('message') }}</textarea>
                            <div class="mt-1 flex items-center justify-between text-xs">
                                <span class="text-gray-500">Dica: você pode anexar imagens na próxima etapa do atendimento.</span>
                                <span class="tabular-nums text-gray-500"><span x-text="msg.length"></span>/<span x-text="max"></span></span>
                            </div>
                            @error('message')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col sm:flex-row sm:justify-end gap-2">
                            <a href="{{ route('cliente.pedidos.index') }}"
                               class="inline-flex justify-center rounded-md border px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancelar
                            </a>
                            <x-primary-button class="inline-flex justify-center bg-pink-600 hover:bg-pink-700 shadow-sm transition">
                                Abrir chamado
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Informações do pedido (opcional) --}}
            <div class="mt-4 sm:mt-6 text-xs text-gray-500">
                Está com urgência? Assim que abrir o chamado, nosso time entra em contato pelo e-mail cadastrado.
            </div>
        </div>
    </div>
</x-app-layout>
