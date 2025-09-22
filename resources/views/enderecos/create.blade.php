{{-- resources/views/enderecos/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2 sm:gap-3">
            <div class="min-w-0">
                <h2 class="truncate font-semibold text-lg sm:text-xl text-gray-800 leading-tight">Novo endereço</h2>
                <h6 class="mt-0.5 text-[10px] sm:text-xs text-gray-500">resources\views\enderecos\create.blade.php</h6>
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
            {{-- Flash messages opcionais --}}
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
                    <form method="POST" action="{{ route('enderecos.store') }}" class="grid gap-4 sm:gap-6">
                        @csrf

                        {{-- Partial com os campos do endereço --}}
                        @include('enderecos._form', ['submitLabel' => 'Cadastrar'])

                        {{-- Ajuda opcional abaixo do formulário (mobile-first) --}}
                        <p class="text-xs text-gray-500">
                            Dica: verifique CEP e número. Você pode editar esse endereço depois.
                        </p>
                    </form>
                </div>
            </div>

            {{-- Ação extra (voltar) para telas pequenas --}}
            <div class="mt-3 flex sm:hidden">
                @if(Route::has('enderecos.index'))
                    <a href="{{ route('enderecos.index') }}"
                       class="w-full inline-flex justify-center rounded-md border px-4 py-2 text-sm font-medium hover:bg-gray-50">
                        Voltar para lista
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
