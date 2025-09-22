{{-- resources/views/enderecos/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2 sm:gap-3">
            <div class="min-w-0">
                <h2 class="truncate font-semibold text-lg sm:text-xl text-gray-800 leading-tight">Editar endereço</h2>
                <h6 class="mt-0.5 text-[10px] sm:text-xs text-gray-500">resources\views\enderecos\edit.blade.php</h6>
            </div>
            <div class="w-full sm:w-auto">
                @if(Route::has('enderecos.index'))
                    <a href="{{ route('enderecos.index') }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center rounded-md border px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        ← Meus endereços
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto w-full max-w-3xl px-4 sm:px-6 lg:px-8">
            {{-- Mensagens flash (opcionais) --}}
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
                        action="{{ route('enderecos.update', $address) }}"
                        class="grid gap-4 sm:gap-6"
                    >
                        @csrf
                        @method('PUT')

                        {{-- Partial com os campos (mantém consistência entre create/edit) --}}
                        @include('enderecos._form', [
                            'address' => $address,
                            'submitLabel' => 'Salvar alterações'
                        ])

                        {{-- Ações auxiliares (mobile-first) --}}
                        <div class="flex flex-col sm:flex-row sm:justify-between gap-2">
                            @if(Route::has('enderecos.destroy'))
                                {{-- Se houver rota para excluir (opcional) --}}
                                <form method="POST" action="{{ route('enderecos.destroy', $address) }}"
                                      onsubmit="return confirm('Tem certeza que deseja excluir este endereço?');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="w-full sm:w-auto inline-flex justify-center rounded-md border px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">
                                        Excluir endereço
                                    </button>
                                </form>
                            @endif

                            @if(Route::has('enderecos.index'))
                                <a href="{{ route('enderecos.index') }}"
                                   class="w-full sm:w-auto inline-flex justify-center rounded-md border px-4 py-2 text-sm font-medium hover:bg-gray-50">
                                    Voltar para lista
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Link extra para voltar (apenas mobile) --}}
            <div class="mt-3 flex sm:hidden">
                @if(Route::has('enderecos.index'))
                    <a href="{{ route('enderecos.index') }}"
                       class="w-full inline-flex justify-center rounded-md border px-4 py-2 text-sm font-medium hover:bg-gray-50">
                        ← Meus endereços
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
