<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $product->name }}
            </h2>
            <h6 class="text-gray-400 text-sm">resources/views/produto/show.blade.php</h6>
            <a href="{{ url('/') }}" class="text-sm text-indigo-600 hover:text-indigo-800 transition">← Voltar para a vitrine</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-2xl p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    {{-- Imagem do produto --}}
                    <div class="rounded-2xl overflow-hidden bg-gray-100 shadow-inner">
                        <img
                            src="{{ $product->cover_url }}"
                            alt="{{ $product->name }}"
                            loading="lazy"
                            decoding="async"
                            fetchpriority="low"
                            onerror="this.onerror=null;this.src='https://picsum.photos/seed/p{{ $product->id }}/800/800'; this.alt='Imagem de exemplo';"
                            class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                        >
                    </div>

                    {{-- Informações do produto --}}
                    <div class="flex flex-col gap-6">
                        <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900">{{ $product->name }}</h1>

                        <div class="text-4xl sm:text-5xl font-extrabold text-pink-600">
                            R$ {{ number_format($product->price, 2, ',', '.') }}
                        </div>

                        {{-- Estoque --}}
                        <div class="flex items-center gap-3">
                            @if($product->stock > 0)
                                <span class="text-sm font-medium rounded-full bg-green-50 text-green-700 px-3 py-1">Em estoque</span>
                                <span class="text-sm text-gray-500">({{ $product->stock }} un.)</span>
                            @else
                                <span class="text-sm font-medium rounded-full bg-gray-200 text-gray-700 px-3 py-1">Esgotado</span>
                            @endif
                        </div>

                        {{-- Descrição --}}
                        <div class="prose max-w-none text-gray-700">
                            {!! nl2br(e($product->description)) !!}
                        </div>

                        {{-- Ações --}}
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                            @if($product->stock > 0)
                                <form method="POST" action="{{ route('cart.add', $product) }}" class="flex items-center gap-3">
                                    @csrf
                                    <input type="number" name="qty" min="1" value="1"
                                        class="w-20 rounded-md border border-gray-300 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-pink-500">
                                    <x-primary-button class="bg-pink-600 hover:bg-pink-700 transition-all duration-200">
                                        Adicionar ao carrinho
                                    </x-primary-button>
                                </form>
                            @else
                                <button type="button" disabled aria-disabled="true"
                                    class="inline-flex items-center rounded-lg bg-gray-300 px-5 py-3 text-sm font-semibold text-white cursor-not-allowed">
                                    Indisponível
                                </button>
                            @endif

                            <a href="{{ url('/') }}" class="text-sm text-gray-600 hover:text-gray-900 transition">Continuar comprando</a>
                        </div>

                        {{-- Direitos autorais --}}
                        <div class="text-center text-xs text-gray-500 leading-relaxed space-y-2 mt-6">
                            <p class="font-semibold">
                                Produtos mostrados neste site pertencem a
                                <a href="https://cakemehometonight.com/" target="_blank" class="hover:underline text-gray-600">
                                    Cake Me Home Tonight
                                </a>
                                e são usados apenas como exemplo para fins de estudo.
                            </p>
                            <p class="font-semibold">
                                Products shown on this site belong to
                                <a href="https://cakemehometonight.com/" target="_blank" class="hover:underline text-gray-600">
                                    Cake Me Home Tonight
                                </a>
                                and are used only as examples for study purposes.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Produtos relacionados --}}
                @if($related->count())
                    <div class="mt-12">
                        <h3 class="text-xl font-semibold mb-6">Você também pode gostar</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                            @foreach ($related as $p)
                                <article class="group bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300">
                                    <a href="{{ route('produto.show', $p->slug ?? $p->id) }}">
                                        <div class="aspect-[4/3] overflow-hidden bg-gray-100">
                                            <img
                                                src="{{ $p->cover_url }}"
                                                alt="{{ $p->name }}"
                                                loading="lazy"
                                                decoding="async"
                                                onerror="this.onerror=null;this.src='https://picsum.photos/seed/p{{ $p->id }}/640/480';"
                                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                            >
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
    </div>
</x-app-layout>
