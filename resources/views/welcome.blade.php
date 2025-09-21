<x-app-layout>
    {{-- HERO --}}
    <section class="relative isolate overflow-hidden h-[340px] sm:h-[440px] lg:h-[520px]">
        <img
            src="https://images.unsplash.com/photo-1488477304112-4944851de03d?q=80&w=2000&auto=format&fit=crop"
            alt="Cupcakes artesanais com coberturas coloridas"
            class="absolute inset-0 -z-20 h-full w-full object-cover"
            loading="eager" fetchpriority="high"
            onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1504754524776-8f4f37790ca0?q=80&w=2000&auto=format&fit=crop';" />

        <div class="absolute inset-0 -z-10 bg-pink-800/40 mix-blend-multiply"></div>
        <div class="absolute inset-0 -z-10 bg-gradient-to-tr from-pink-900/60 via-pink-600/35 to-transparent"></div>
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

    {{-- PRODUTOS --}}
    <section id="produtos" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 mt-[10px]">
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
                    <x-product-card
                        :name="$p->name"
                        :price="$p->price"
                        :img="$p->cover_url"
                        :url="Route::has('produto.show') ? route('produto.show', $p->slug ?? $p->id) : null"
                        :in-stock="$p->stock > 0"
                        :model="$p"
                    />
                @empty
                    <p class="text-gray-500">Nenhum produto ativo ainda.</p>
                @endforelse
            @else
                @foreach ($placeholders as $p)
                    <x-product-card
                        :name="$p['nome']"
                        :price="$p['preco']"
                        :img="$p['img']"
                        :in-stock="false"
                    />
                @endforeach
            @endisset
        </div>

        @if(isset($produtos) && method_exists($produtos, 'links'))
            <div class="mt-8">{{ $produtos->links() }}</div>
        @endif
    </section>
</x-app-layout>
