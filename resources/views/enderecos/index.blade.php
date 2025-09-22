{{-- resources/views/enderecos/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2 sm:gap-3">
            <div class="min-w-0">
                <h2 class="truncate font-semibold text-lg sm:text-xl text-gray-800 leading-tight">Meus endereços</h2>
                <h6 class="mt-0.5 text-[10px] sm:text-xs text-gray-500">resources\views\enderecos\index.blade.php</h6>
            </div>

            @if(Route::has('enderecos.create'))
                <div class="w-full sm:w-auto">
                    <a href="{{ route('enderecos.create') }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Novo endereço
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 rounded-lg bg-green-50 text-green-800 px-4 py-3 border border-green-200">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-lg bg-red-50 text-red-800 px-4 py-3 border border-red-200">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    @if ($addresses->isEmpty())
                        <p class="text-gray-600">Você ainda não cadastrou endereços.</p>
                        @if(Route::has('enderecos.create'))
                            <a href="{{ route('enderecos.create') }}"
                               class="mt-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                Cadastrar endereço
                            </a>
                        @endif
                    @else
                        {{-- LISTA MOBILE (CARDS) — < sm --}}
                        <div class="space-y-3 sm:hidden">
                            @foreach ($addresses as $a)
                                @php
                                    $cep = preg_replace('/[^0-9]/','', (string)$a->cep);
                                    if (strlen($cep) === 8) {
                                        $cep = substr($cep,0,5).'-'.substr($cep,5,3);
                                    } else {
                                        $cep = $cep ?: '—';
                                    }
                                @endphp

                                <div class="rounded-lg border border-gray-200 p-3">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $a->logradouro }}, {{ $a->numero }}
                                        @if($a->complemento) — {{ $a->complemento }} @endif
                                        @if($a->bairro) — {{ $a->bairro }} @endif
                                    </div>
                                    <div class="mt-1 text-xs text-gray-600">
                                        {{ $a->cidade }}/{{ strtoupper($a->estado) }} · CEP: {{ $cep }}
                                    </div>

                                    <div class="mt-3 flex flex-col xs:flex-row gap-2">
                                        <a href="{{ route('enderecos.edit', $a) }}"
                                           class="w-full xs:w-auto inline-flex justify-center rounded-md border px-3 py-2 text-sm font-medium hover:bg-gray-50">
                                            Editar
                                        </a>
                                        <form method="POST" action="{{ route('enderecos.destroy', $a) }}" class="w-full xs:w-auto"
                                              onsubmit="return confirm('Excluir este endereço?');">
                                            @csrf @method('DELETE')
                                            <button
                                                class="w-full inline-flex justify-center rounded-md border px-3 py-2 text-sm font-medium hover:bg-gray-50">
                                                Excluir
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach

                            <div class="pt-2">
                                {{ $addresses->withQueryString()->links() }}
                            </div>
                        </div>

                        {{-- TABELA DESKTOP — ≥ sm --}}
                        <div class="hidden sm:block">
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="text-left text-gray-500 border-b">
                                    <tr>
                                        <th class="py-2 pr-4">Endereço</th>
                                        <th class="py-2 pr-4">Cidade/UF</th>
                                        <th class="py-2 pr-4">CEP</th>
                                        <th class="py-2 pr-4 w-48">Ações</th>
                                    </tr>
                                    </thead>
                                    <tbody class="divide-y">
                                    @foreach ($addresses as $a)
                                        @php
                                            $cep = preg_replace('/[^0-9]/','', (string)$a->cep);
                                            if (strlen($cep) === 8) {
                                                $cep = substr($cep,0,5).'-'.substr($cep,5,3);
                                            } else {
                                                $cep = $cep ?: '—';
                                            }
                                        @endphp
                                        <tr>
                                            <td class="py-3 pr-4 max-w-[520px]">
                                                <div class="truncate">
                                                    {{ $a->logradouro }}, {{ $a->numero }}
                                                    @if($a->complemento) — {{ $a->complemento }} @endif
                                                    @if($a->bairro) — {{ $a->bairro }} @endif
                                                </div>
                                            </td>
                                            <td class="py-3 pr-4 whitespace-nowrap">
                                                {{ $a->cidade }}/{{ strtoupper($a->estado) }}
                                            </td>
                                            <td class="py-3 pr-4 whitespace-nowrap">{{ $cep }}</td>
                                            <td class="py-3 pr-4">
                                                <div class="flex flex-wrap gap-2">
                                                    <a href="{{ route('enderecos.edit', $a) }}"
                                                       class="inline-flex items-center rounded-md border px-3 py-1.5 text-xs font-medium hover:bg-gray-50">
                                                        Editar
                                                    </a>
                                                    <form method="POST" action="{{ route('enderecos.destroy', $a) }}"
                                                          onsubmit="return confirm('Excluir este endereço?');">
                                                        @csrf @method('DELETE')
                                                        <button
                                                            class="inline-flex items-center rounded-md border px-3 py-1.5 text-xs font-medium hover:bg-gray-50">
                                                            Excluir
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-6">
                                {{ $addresses->withQueryString()->links() }}
                            </div>
                        </div>
                    @endif
                </div> {{-- /p --}}
            </div> {{-- /card --}}
        </div>
    </div>
</x-app-layout>
