{{-- resources/views/produto/show.blade.php (versão responsiva) --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <div class="min-w-0 flex-1">
                <h2 class="truncate font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
                    {{ $product->name }}
                </h2>
                <h6 class="mt-0.5 hidden sm:block text-[10px] sm:text-xs text-gray-400">
                    resources/views/produto/show.blade.php
                </h6>
            </div>
            <div class="w-full sm:w-auto">
                <a href="{{ url('/') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center text-sm text-indigo-600 hover:text-indigo-800 transition">
                    ← Voltar para a vitrine
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6 lg:py-8">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:shadow-lg sm:rounded-2xl">
                <div class="p-4 sm:p-6 lg:p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
                        {{-- Imagem do produto --}}
                        <div class="lg:sticky lg:top-6">
                            <div class="rounded-2xl overflow-hidden bg-gray-100 ring-1 ring-gray-200 aspect-square">
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
                        </div>

                        {{-- Informações do produto --}}
                        <div class="flex flex-col gap-4 sm:gap-5">
                            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900">
                                {{ $product->name }}
                            </h1>

                            <div class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-pink-600">
                                R$ {{ number_format($product->price, 2, ',', '.') }}
                            </div>

                            {{-- Estoque --}}
                            <div class="flex flex-wrap items-center gap-2">
                                @if($product->stock > 0)
                                    <span class="text-xs sm:text-sm font-medium rounded-full bg-green-50 text-green-700 px-3 py-1">Em estoque</span>
                                    <span class="text-xs sm:text-sm text-gray-500">({{ $product->stock }} un.)</span>
                                @else
                                    <span class="text-xs sm:text-sm font-medium rounded-full bg-gray-200 text-gray-700 px-3 py-1">Esgotado</span>
                                @endif
                            </div>

                            {{-- Descrição --}}
                            <div class="prose prose-sm sm:prose base max-w-none text-gray-700">
                                {!! nl2br(e($product->description)) !!}
                            </div>

                            {{-- Ações --}}
                            <div class="mt-2 flex flex-col sm:flex-row sm:items-center gap-3 w-full">
                                @if($product->stock > 0)
                                    <form
                                        method="POST"
                                        action="{{ route('cart.add', $product) }}"
                                        class="w-full sm:w-auto flex flex-col xxs:flex-row items-stretch xxs:items-center gap-2"
                                    >
                                        @csrf
                                        <label for="qty" class="sr-only">Quantidade</label>
                                        <input
                                            id="qty"
                                            type="number"
                                            name="qty"
                                            min="1"
                                            value="1"
                                            inputmode="numeric"
                                            class="w-full xxs:w-24 rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-pink-500"
                                        >
                                        <x-primary-button class="w-full xxs:w-auto bg-pink-600 hover:bg-pink-700 transition">
                                            Adicionar ao carrinho
                                        </x-primary-button>
                                    </form>
                                @else
                                    <button type="button" disabled aria-disabled="true"
                                            class="w-full sm:w-auto inline-flex items-center justify-center rounded-lg bg-gray-300 px-5 py-3 text-sm font-semibold text-white cursor-not-allowed">
                                        Indisponível
                                    </button>
                                @endif

                                <a href="{{ url('/') }}" class="w-full sm:w-auto inline-flex items-center justify-center text-sm text-gray-600 hover:text-gray-900 transition">
                                    Continuar comprando
                                </a>
                            </div>

                            {{-- Aviso de direitos autorais (colapsável no mobile) --}}
                            <details class="mt-3 rounded-md bg-gray-50 p-3 text-xs text-gray-600 [&_a]:underline [&_a]:decoration-gray-400">
                                <summary class="cursor-pointer select-none font-medium text-gray-700">Aviso de direitos autorais</summary>
                                <div class="mt-2 space-y-2">
                                    <p class="font-semibold">
                                        Produtos mostrados neste site pertencem a
                                        <a href="https://cakemehometonight.com/" target="_blank" rel="noopener">Cake Me Home Tonight</a>
                                        e são usados apenas como exemplo para fins de estudo.
                                    </p>
                                    <p class="font-semibold">
                                        Products shown on this site belong to
                                        <a href="https://cakemehometonight.com/" target="_blank" rel="noopener">Cake Me Home Tonight</a>
                                        and are used only as examples for study purposes.
                                    </p>
                                </div>
                            </details>
                        </div>
                    </div>

                    {{-- Produtos relacionados --}}
                    @if($related->count())
                        <div class="mt-10 sm:mt-12">
                            <div class="flex items-center justify-between mb-3 sm:mb-6">
                                <h3 class="text-lg sm:text-xl font-semibold">Você também pode gostar</h3>
                                {{-- Scroll hint no mobile --}}
                                <span class="sm:hidden text-xs text-gray-500">Arraste →</span>
                            </div>

                            {{-- Grid no desktop / carrossel horizontal no mobile --}}
                            <div class="sm:hidden -mx-4 px-4">
                                <div class="flex gap-4 overflow-x-auto snap-x snap-mandatory pb-2">
                                    @foreach ($related as $p)
                                        <article class="min-w-[72%] xxs:min-w-[60%] snap-start group bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
                                            <a href="{{ route('produto.show', $p->slug ?? $p->id) }}" class="block">
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
                                                <div class="p-3">
                                                    <h4 class="font-semibold line-clamp-2">{{ $p->name }}</h4>
                                                    <div class="mt-1 text-base font-bold text-gray-900">
                                                        R$ {{ number_format($p->price, 2, ',', '.') }}
                                                    </div>
                                                </div>
                                            </a>
                                        </article>
                                    @endforeach
                                </div>
                            </div>

                            <div class="hidden sm:grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                                @foreach ($related as $p)
                                    <article class="group bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300">
                                        <a href="{{ route('produto.show', $p->slug ?? $p->id) }}" class="block">
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
                                            <div class="p-4">
                                                <h4 class="font-semibold line-clamp-2">{{ $p->name }}</h4>
                                                <div class="mt-2 text-lg font-bold text-gray-900">
                                                    R$ {{ number_format($p->price, 2, ',', '.') }}
                                                </div>
                                            </div>
                                        </a>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
