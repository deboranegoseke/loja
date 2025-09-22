{{-- resources/views/home.blade.php (vitrine responsiva) --}}
<x-app-layout>
    {{-- HERO — compacto e responsivo --}}
    <section class="relative isolate overflow-hidden h-[220px] sm:h-[300px] lg:h-[360px]">
        {{-- Imagem de fundo com fallback --}}
        <img
            src="https://images.unsplash.com/photo-1488477304112-4944851de03d?q=80&w=2000&auto=format&fit=crop"
            alt="Cupcakes artesanais com coberturas coloridas"
            class="absolute inset-0 -z-20 h-full w-full object-cover"
            loading="eager"
            fetchpriority="high"
            onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1504754524776-8f4f37790ca0?q=80&w=2000&auto=format&fit=crop';"
        />

        {{-- Overlays decorativos --}}
        <div class="absolute inset-0 -z-10 bg-pink-800/40 mix-blend-multiply"></div>
        <div class="absolute inset-0 -z-10 bg-gradient-to-tr from-pink-900/60 via-pink-600/35 to-transparent"></div>
        <div
            class="pointer-events-none absolute -z-10 -left-16 top-10 h-60 w-60 rounded-full blur-3xl
                   bg-[radial-gradient(ellipse_at_center,theme(colors.pink.400/.40),transparent_60%)]">
        </div>

        {{-- Texto centralizado --}}
        <div class="max-w-7xl mx-auto h-full px-4 sm:px-6 lg:px-8 flex items-center justify-center text-center">
            <div class="max-w-xl text-white drop-shadow-[0_2px_4px_rgba(0,0,0,0.45)]">
                <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
                    Cupcakes que encantam pelo sabor
                </h1>
                <p class="mt-2 text-sm sm:text-lg text-pink-100 font-medium">
                    Artesanais, criativos e feitos para celebrar cada momento.
                </p>
            </div>
        </div>
    </section>

    {{-- VITRINE DE PRODUTOS --}}
    <section id="produtos" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12">
        <div class="flex items-end justify-between gap-2">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-800">
                Nossa Vitrine de Sabores
            </h2>

            {{-- Dica de scroll no mobile (quando houver muitos cards) --}}
            <span class="sm:hidden text-xs text-gray-500">Toque e role ↓</span>
        </div>

        {{-- Grid com quebra fluida por breakpoint --}}
        <div class="mt-6 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @isset($produtos)
                @forelse ($produtos as $p)
                    <article class="group bg-white rounded-2xl border border-gray-200 overflow-hidden ring-1 ring-transparent hover:ring-pink-200 hover:shadow-lg transition">
                        <a
                            @if(Route::has('produto.show'))
                                href="{{ route('produto.show', $p->slug ?? $p->id) }}"
                            @else
                                href="#"
                            @endif
                            class="block"
                            aria-label="Ver detalhes de {{ $p->name }}"
                        >
                            <div class="aspect-[4/3] overflow-hidden bg-gray-100">
                                <img
                                    src="{{ $p->cover_url }}"
                                    alt="{{ $p->name }}"
                                    loading="lazy"
                                    decoding="async"
                                    fetchpriority="low"
                                    onerror="this.onerror=null;this.src='https://picsum.photos/seed/p{{ $p->id }}/640/480';"
                                    class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                                >
                            </div>
                        </a>

                        <div class="p-4">
                            <h3 class="font-semibold line-clamp-2">
                                @if(Route::has('produto.show'))
                                    <a href="{{ route('produto.show', $p->slug ?? $p->id) }}" class="hover:underline">
                                        {{ $p->name }}
                                    </a>
                                @else
                                    {{ $p->name }}
                                @endif
                            </h3>

                            <div class="mt-3 flex items-center justify-between gap-3">
                                <span class="text-lg font-bold text-gray-900">
                                    R$ {{ number_format($p->price, 2, ',', '.') }}
                                </span>

                                @if($p->stock > 0 && Route::has('cart.add'))
                                    <form method="POST" action="{{ route('cart.add', $p) }}" class="contents">
                                        @csrf
                                        <input type="hidden" name="qty" value="1">
                                        <button
                                            class="inline-flex items-center rounded-lg bg-pink-600 px-3 py-2 text-sm font-semibold text-white hover:bg-pink-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-400 focus-visible:ring-offset-2">
                                            Adicionar
                                        </button>
                                    </form>
                                @elseif($p->stock > 0)
                                    <span class="text-xs text-gray-500">Carrinho indisponível</span>
                                @else
                                    <span class="text-xs rounded-full bg-gray-100 text-gray-700 px-2 py-1">Esgotado</span>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="text-gray-500 col-span-full text-center">Nenhum produto ativo ainda.</p>
                @endforelse
            @else
                @foreach ($placeholders as $p)
                    <article class="group bg-white rounded-2xl border border-gray-200 overflow-hidden ring-1 ring-transparent hover:ring-pink-200 hover:shadow-lg transition">
                        <div class="aspect-[4/3] overflow-hidden bg-gray-100">
                            <img
                                src="{{ $p['img'] }}"
                                alt="{{ $p['nome'] }}"
                                loading="lazy"
                                decoding="async"
                                fetchpriority="low"
                                class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                            >
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold line-clamp-2">{{ $p['nome'] }}</h3>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-lg font-bold text-gray-900">
                                    R$ {{ number_format($p['preco'] ?? 0, 2, ',', '.') }}
                                </span>
                                <button
                                    type="button"
                                    class="inline-flex items-center rounded-lg bg-pink-600 px-3 py-2 text-sm font-semibold text-white hover:bg-pink-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-400 focus-visible:ring-offset-2"
                                    onclick="alert('Carrinho ainda não disponível.');">
                                    Adicionar
                                </button>
                            </div>
                        </div>
                    </article>
                @endforeach
            @endisset
        </div>

        {{-- Paginação (preserva filtros se existirem) --}}
        @if(isset($produtos) && method_exists($produtos, 'links'))
            <div class="mt-8">
                {{ $produtos->withQueryString()->links() }}
            </div>
        @endif
    </section>
</x-app-layout>
