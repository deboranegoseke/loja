<x-app-layout>
    {{-- HERO — Cupcakes com overlay forte + glow --}}
    <section class="relative isolate overflow-hidden h-[340px] sm:h-[440px] lg:h-[520px]">
        {{-- imagem de fundo (pode trocar por Vite::asset se usar local) --}}
        <img
            src="https://images.unsplash.com/photo-1488477304112-4944851de03d?q=80&w=2000&auto=format&fit=crop"
            alt="Cupcakes artesanais com coberturas coloridas"
            class="absolute inset-0 -z-20 h-full w-full object-cover"
            loading="eager" fetchpriority="high"
            onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1504754524776-8f4f37790ca0?q=80&w=2000&auto=format&fit=crop';"
        />

        {{-- camada 1: tom rosado multiplicado (garante cor em qualquer foto) --}}
        <div class="absolute inset-0 -z-10 bg-pink-800/40 mix-blend-multiply"></div>

        {{-- camada 2: gradiente direcional para contraste do texto --}}
        <div class="absolute inset-0 -z-10 bg-gradient-to-tr from-pink-900/60 via-pink-600/35 to-transparent"></div>

        {{-- camada 3: brilho radial discreto atrás do título --}}
        <div class="pointer-events-none absolute -z-10 -left-16 top-10 h-80 w-80 rounded-full blur-3xl
                    bg-[radial-gradient(ellipse_at_center,theme(colors.pink.400/.40),transparent_60%)]"></div>

        <div class="max-w-7xl mx-auto h-full px-4 sm:px-6 lg:px-8 flex items-center">
            <div class="max-w-2xl text-white drop-shadow-[0_1px_1px_rgba(0,0,0,0.35)]">


                <h1 class="mt-3 text-4xl sm:text-5xl font-extrabold tracking-tight">
                    Cupcakes feitos com <span class="text-pink-200">amor</span> e ingredientes de verdade
                </h1>

                <p class="mt-3 text-base sm:text-lg text-white/95">
                    Sabores do dia: Red Velvet, Brigadeiro, Limão Siciliano 🍋
                </p>

                <div class="mt-6 flex flex-col sm:flex-row gap-3">
                    <a href="#produtos"
                       class="inline-flex justify-center rounded-lg bg-pink-600 px-6 py-3 text-base font-semibold text-white hover:bg-pink-700">
                        Ver sabores
                    </a>

                    @guest
                        <a href="{{ route('register') }}"
                           class="inline-flex justify-center rounded-lg bg-white/10 px-6 py-3 text-base font-semibold text-white ring-1 ring-white/40 hover:bg-white/20">
                            Criar conta
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex justify-center rounded-lg bg-white px-6 py-3 text-base font-semibold text-gray-900 ring-1 ring-gray-200 hover:bg-gray-50">
                            Acompanhar pedidos
                        </a>
                    @endguest
                </div>


            </div>
        </div>
    </section>

    {{-- CATEGORIAS (tema cupcakes) --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-wrap gap-2">
            @php
                $categorias = ['Clássicos','Chocolate','Red Velvet','Frutas','Diet/Zero','Sem Glúten','Veganos','Kits Festa'];
            @endphp
            @foreach ($categorias as $cat)
                <span class="inline-flex items-center rounded-full border border-gray-200 bg-white px-4 py-2 text-sm
                             hover:border-pink-300 hover:text-pink-700 cursor-default">
                    {{ $cat }}
                </span>
            @endforeach
        </div>
    </section>

    {{-- PRODUTOS --}}
    <section id="produtos" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        @php
            $placeholders = [
                ['nome'=>'Cupcake Red Velvet','preco'=>15.90,'img'=>'https://images.unsplash.com/photo-1551024709-8f23befc6cf7?w=1200&auto=format&fit=crop'],
                ['nome'=>'Cupcake Brigadeiro','preco'=>12.90,'img'=>'https://images.unsplash.com/photo-1606313564200-e75d5e30476f?w=1200&auto=format&fit=crop'],
                ['nome'=>'Cupcake Limão Siciliano','preco'=>13.90,'img'=>'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=1200&auto=format&fit=crop'],
                ['nome'=>'Cupcake Frutas Vermelhas','preco'=>14.90,'img'=>'https://images.unsplash.com/photo-1490474418585-ba9bad8fd0ea?w=1200&auto=format&fit=crop'],
            ];
        @endphp

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @isset($produtos)
                @forelse ($produtos as $p)
                    <article class="group bg-white rounded-2xl border border-gray-200 overflow-hidden ring-1 ring-transparent hover:ring-pink-200 hover:shadow-lg transition">
                        <div class="aspect-[4/3] overflow-hidden bg-gray-100">
                            <img
                                src="{{ $p->cover_url }}" alt="{{ $p->name }}"
                                loading="lazy" decoding="async" fetchpriority="low"
                                onerror="this.onerror=null;this.src='https://picsum.photos/seed/p{{ $p->id }}/640/480';"
                                class="h-full w-full object-cover group-hover:scale-105 transition duration-300">
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold line-clamp-2">
                                @if(Route::has('produto.show'))
                                    <a href="{{ route('produto.show', $p->slug ?? $p->id) }}" class="hover:underline">{{ $p->name }}</a>
                                @else
                                    {{ $p->name }}
                                @endif
                            </h3>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-lg font-bold text-gray-900">
                                    R$ {{ number_format($p->price, 2, ',', '.') }}
                                </span>
                                @if($p->stock > 0)
    <form method="POST" action="{{ route('cart.add', $p) }}">
        @csrf
        <input type="hidden" name="qty" value="1">
        <button class="inline-flex items-center rounded-lg bg-pink-600 px-3 py-2 text-sm font-semibold text-white hover:bg-pink-700">
            Adicionar
        </button>
    </form>
@else
    <span class="text-xs rounded-full bg-gray-100 text-gray-700 px-2 py-1">Esgotado</span>
@endif
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="text-gray-500">Nenhum produto ativo ainda.</p>
                @endforelse
            @else
                @foreach ($placeholders as $p)
                    <article class="group bg-white rounded-2xl border border-gray-200 overflow-hidden ring-1 ring-transparent hover:ring-pink-200 hover:shadow-lg transition">
                        <div class="aspect-[4/3] overflow-hidden bg-gray-100">
                            <img src="{{ $p['img'] }}" alt="{{ $p['nome'] }}"
                                 loading="lazy" decoding="async" fetchpriority="low"
                                 class="h-full w-full object-cover group-hover:scale-105 transition duration-300">
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold line-clamp-2">{{ $p['nome'] }}</h3>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-lg font-bold text-gray-900">
                                    R$ {{ number_format($p['preco'] ?? 0, 2, ',', '.') }}
                                </span>
                                <button type="button"
                                    class="inline-flex items-center rounded-lg bg-pink-600 px-3 py-2 text-sm font-semibold text-white hover:bg-pink-700"
                                    onclick="alert('Carrinho ainda não disponível.');">
                                    Adicionar
                                </button>
                            </div>
                        </div>
                    </article>
                @endforeach
            @endisset
        </div>

        @if(isset($produtos) && method_exists($produtos, 'links'))
            <div class="mt-8">{{ $produtos->links() }}</div>
        @endif
    </section>
</x-app-layout>
