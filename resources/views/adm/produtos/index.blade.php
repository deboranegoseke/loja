<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Produtos</h2>
            <a href="{{ route('adm.produtos.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                Novo produto
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-green-50 text-green-800 px-4 py-3 border border-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-0 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produto</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Preço</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estoque</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ativo</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($products as $p)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $p->cover_url }}" class="h-12 w-12 rounded object-cover border" alt="">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $p->name }}</div>
                                            <div class="text-xs text-gray-500">SKU: {{ $p->sku ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">R$ {{ number_format($p->price, 2, ',', '.') }}</td>
                                <td class="px-4 py-3">{{ $p->stock }}</td>
                                <td class="px-4 py-3">
                                    @if($p->active)
                                        <span class="text-xs rounded-full bg-green-50 text-green-700 px-2 py-1">ativo</span>
                                    @else
                                        <span class="text-xs rounded-full bg-gray-100 text-gray-700 px-2 py-1">inativo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('adm.produtos.edit', $p) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">Editar</a>
                                    <form action="{{ route('adm.produtos.destroy', $p) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="ml-3 text-red-600 hover:text-red-800 text-sm" onclick="return confirm('Remover este produto?')">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-10 text-center text-gray-500">Nenhum produto.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3">{{ $products->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
