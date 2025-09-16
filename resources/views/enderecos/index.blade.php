<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Meus endereços</h2>
                <h6>resources\views\enderecos\index.blade.php</h6>
            </div>

            @if(Route::has('enderecos.create'))
                <a href="{{ route('enderecos.create') }}"
                   class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                    Novo endereço
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

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

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if ($addresses->isEmpty())
                    <p class="text-gray-600">Você ainda não cadastrou endereços.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-left text-gray-500 border-b">
                            <tr>
                                <th class="py-2 pr-4">Endereço</th>
                                <th class="py-2 pr-4">Cidade/UF</th>
                                <th class="py-2 pr-4">CEP</th>
                                <th class="py-2 pr-4 w-40">Ações</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y">
                            @foreach ($addresses as $a)
                                @php
                                    $cep = preg_replace('/[^0-9]/','', (string)$a->cep);
                                    if (strlen($cep) === 8) {
                                        $cep = substr($cep,0,5).'-'.substr($cep,5,3);
                                    }
                                @endphp
                                <tr>
                                    <td class="py-3 pr-4">
                                        {{ $a->logradouro }}, {{ $a->numero }}
                                        @if($a->complemento) — {{ $a->complemento }} @endif
                                        @if($a->bairro) — {{ $a->bairro }} @endif
                                    </td>
                                    <td class="py-3 pr-4">{{ $a->cidade }}/{{ strtoupper($a->estado) }}</td>
                                    <td class="py-3 pr-4">{{ $cep ?: '—' }}</td>
                                    <td class="py-3 pr-4">
                                        <a href="{{ route('enderecos.edit', $a) }}"
                                           class="inline-flex items-center rounded-md border px-3 py-1.5 text-xs font-medium hover:bg-gray-50">
                                            Editar
                                        </a>
                                        <form method="POST" action="{{ route('enderecos.destroy', $a) }}" class="inline">
                                            @csrf @method('DELETE')
                                            <button class="ml-2 inline-flex items-center rounded-md border px-3 py-1.5 text-xs font-medium hover:bg-gray-50"
                                                    onclick="return confirm('Excluir este endereço?')">
                                                Excluir
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $addresses->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
