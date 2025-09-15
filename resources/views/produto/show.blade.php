<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $product->name }}
            </h2>
            <h6>resources\views\produto\show.blade.php</h6>
            {{-- Voltar para a vitrine --}}
            <a href="{{ url('/') }}" class="text-sm text-indigo-600 hover:text-indigo-800">← Voltar para a vitrine</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Imagem --}}
                    <div>
                        <div class="aspect-square w-full overflow-hidden rounded-xl bg-gray-100">
                            <img
                                src="{{ $product->cover_url }}"
                                alt="{{ $product->name }}"
                                loading="lazy" decoding="async" fetchpriority="low"
                                onerror="this.onerror=null;this.src='https://picsum.photos/seed/p{{ $product->id }}/800/800';"
                                class="h-full w-full object-cover"
                            >
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="flex flex-col gap-4">
                        <h1 class="text-2xl font-bold">{{ $product->name }}</h1>
                        <div class="text-3xl font-extrabold text-gray-900">
                            R$ {{ number_format($product->price, 2, ',', '.') }}
                        </div>

                        <div class="flex items-center gap-2">
                            @if($product->stock > 0)
                                <span class="text-sm rounded-full bg-green-50 text-green-700 px-2 py-1">Em estoque</span>
                                <span class="text-sm text-gray-500">({{ $product->stock }} un.)</span>
                            @else
                                <span class="text-sm rounded-full bg-gray-100 text-gray-700 px-2 py-1">Esgotado</span>
                            @endif
                        </div>

                        <div class="prose max-w-none">
                            {!! nl2br(e($product->description)) !!}
                        </div>

                        <div class="mt-4 flex items-center gap-3">
                            @if($product->stock > 0)
                                <form method="POST" action="{{ route('cart.add', $product) }}">
                                    @csrf
                                    <input type="number" name="qty" min="1" value="1"
                                           class="w-20 rounded-md border-gray-300 mr-2">
                                    <x-primary-button class="bg-pink-600 hover:bg-pink-700">Adicionar ao carrinho</x-primary-button>
                                </form>
                            @else
                                <button type="button" disabled
                                        class="inline-flex items-center rounded-lg bg-gray-300 px-5 py-3 text-sm font-semibold text-white cursor-not-allowed">
                                    Indisponível
                                </button>
                            @endif
                            <a href="{{ url('/') }}" class="text-sm text-gray-600 hover:text-gray-900">Continuar comprando</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Relacionados --}}
            @if($related->count())
                <div class="mt-8">
                    <h3 class="text-lg font-semibold mb-4">Você também pode gostar</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                        @foreach ($related as $p)
                            <article class="group bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-md transition">
                                <a href="{{ route('produto.show', $p->slug ?? $p->id) }}">
                                    <div class="aspect-[4/3] overflow-hidden bg-gray-100">
                                        <img
                                            src="{{ $p->cover_url }}" alt="{{ $p->name }}"
                                            loading="lazy" decoding="async"
                                            onerror="this.onerror=null;this.src='https://picsum.photos/seed/p{{ $p->id }}/640/480';"
                                            class="h-full w-full object-cover group-hover:scale-105 transition duration-300">
                                    </div>
                                </a>
                                <div class="p-4">
                                    <h4 class="font-semibold line-clamp-2">
                                        <a href="{{ route('produto.show', $p->slug ?? $p->id) }}" class="hover:underline">
                                            {{ $p->name }}
                                        </a>
                                    </h4>
                                    <div class="mt-2 text-lg font-bold text-gray-900">
                                        R$ {{ number_format($p->price, 2, ',', '.') }}
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
