{{-- resources/views/enderecos/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="truncate font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
                Endereço de entrega
            </h2>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">

                    {{-- Aviso LGPD --}}
                    <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900">
                        <div class="flex items-start gap-3">
                            <svg class="mt-0.5 h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M12 2l7 4v6c0 5-3.8 9.7-7 10-3.2-.3-7-5-7-10V6l7-4zm0 4.2L7 7.8v4.2c0 3.8 2.8 7.9 5 8.3 2.2-.4 5-4.5 5-8.3V7.8l-5-1.6z"/>
                                <path d="M11 12.5l4-4 1.4 1.4-5.4 5.4L7.6 11 9 9.6l2 1.9z"/>
                            </svg>
                            <p class="text-sm leading-relaxed">
                                Tratamos seus dados pessoais em conformidade com a
                                <strong>Lei nº 13.709/2018 (LGPD)</strong>. Suas informações são
                                <strong>protegidas</strong> e usadas apenas para finalidades legítimas do serviço,
                                observando princípios de <strong>segurança</strong>, <strong>transparência</strong> e
                                <strong>minimização de dados</strong>.<br>
                                <strong>Este sistema é para fins de estudo; por favor, insira informações fictícias.</strong>
                            </p>
                        </div>
                    </div>
                    {{-- /Aviso LGPD --}}

                    @if ($addresses->isEmpty())
                        <p class="text-gray-600">Você ainda não cadastrou endereços.</p>

                        <div class="mt-3 flex flex-wrap gap-2">
                            @if (Route::has('enderecos.create'))
                                <a href="{{ route('enderecos.create') }}"
                                   class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Cadastrar endereço
                                </a>
                            @endif

                            <a href="{{ url()->previous() ?: route('dashboard') }}"
                               class="inline-flex items-center gap-2 rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300/50">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5A1 1 0 0110.707 4.293L7.414 7.586H17a1 1 0 110 2H7.414l3.293 3.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                </svg>
                                Voltar
                            </a>
                        </div>
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

                                <div class="rounded-lg border border-gray-200 p-3 transition-shadow hover:shadow-sm">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $a->logradouro }}, {{ $a->numero }}
                                        @if ($a->complemento) — {{ $a->complemento }} @endif
                                        @if ($a->bairro) — {{ $a->bairro }} @endif
                                    </div>
                                    <div class="mt-1 text-xs text-gray-600">
                                        {{ $a->cidade }}/{{ strtoupper($a->estado) }} · CEP: {{ $cep }}
                                    </div>

                                    <div class="mt-3 flex flex-col xs:flex-row flex-wrap gap-2">
                                        {{-- Voltar + Editar + Excluir --}}
                                        <a href="{{ url()->previous() ?: route('dashboard') }}"
                                           class="w-full xs:w-auto inline-flex items-center justify-center gap-2 rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300/50">
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5A1 1 0 0110.707 4.293L7.414 7.586H17a1 1 0 110 2H7.414l3.293 3.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                            </svg>
                                            Voltar
                                        </a>

                                        <a href="{{ route('enderecos.edit', $a) }}"
                                           class="w-full xs:w-auto inline-flex items-center justify-center gap-2 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-8.486 8.486a2 2 0 01-.878.515l-3.243.811a.5.5 0 01-.606-.606l.811-3.243a2 2 0 01.515-.878l8.486-8.486z"/>
                                            </svg>
                                            Editar
                                        </a>

                                        <form method="POST" action="{{ route('enderecos.destroy', $a) }}" class="w-full xs:w-auto"
                                              onsubmit="return confirm('Excluir este endereço?');">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                class="w-full inline-flex items-center justify-center gap-2 rounded-md border border-red-300 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-400/30">
                                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 100 2h.293l.853 10.235A2 2 0 007.139 18h5.722a2 2 0 001.993-1.765L15.707 6H16a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zm-1 6a1 1 0 112 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 112 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd"/>
                                                </svg>
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
                                    <thead class="border-b bg-gray-50 text-left text-gray-600">
                                    <tr>
                                        <th class="py-2.5 pr-4 font-semibold">Endereço</th>
                                        <th class="py-2.5 pr-4 font-semibold">Cidade/UF</th>
                                        <th class="py-2.5 pr-4 font-semibold">CEP</th>
                                        <th class="w-64 py-2.5 pr-4 font-semibold">Ações</th>
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
                                        <tr class="hover:bg-gray-50/50">
                                            <td class="max-w-[520px] py-3.5 pr-4">
                                                <div class="truncate text-gray-900">
                                                    {{ $a->logradouro }}, {{ $a->numero }}
                                                    @if ($a->complemento) — {{ $a->complemento }} @endif
                                                    @if ($a->bairro) — {{ $a->bairro }} @endif
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap py-3.5 pr-4 text-gray-700">
                                                {{ $a->cidade }}/{{ strtoupper($a->estado) }}
                                            </td>
                                            <td class="whitespace-nowrap py-3.5 pr-4 text-gray-700">
                                                {{ $cep }}
                                            </td>
                                            <td class="py-3.5 pr-4">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <a href="{{ url()->previous() ?: route('dashboard') }}"
                                                       class="inline-flex items-center gap-2 rounded-md border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300/50">
                                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                            <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5A1 1 0 0110.707 4.293L7.414 7.586H17a1 1 0 110 2H7.414l3.293 3.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Voltar
                                                    </a>

                                                    <a href="{{ route('enderecos.edit', $a) }}"
                                                       class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-8.486 8.486a2 2 0 01-.878.515l-3.243.811a.5.5 0 01-.606-.606l.811-3.243a2 2 0 01.515-.878l8.486-8.486z"/>
                                                        </svg>
                                                        Editar
                                                    </a>

                                                    <form method="POST" action="{{ route('enderecos.destroy', $a) }}"
                                                          onsubmit="return confirm('Excluir este endereço?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button
                                                            class="inline-flex items-center gap-2 rounded-md border border-red-300 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-400/30">
                                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 100 2h.293l.853 10.235A2 2 0 007.139 18h5.722a2 2 0 001.993-1.765L15.707 6H16a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zm-1 6a1 1 0 112 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 112 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd"/>
                                                            </svg>
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
