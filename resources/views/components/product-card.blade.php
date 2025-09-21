@props([
    'name' => 'Produto',
    'price' => 0,
    'img' => null,
    'url' => null,
    'inStock' => true,
    'model' => null, // se passar um Model (ex: Product)
])

<article
    class="group bg-white rounded-2xl border border-gray-200 overflow-hidden ring-1 ring-transparent hover:ring-pink-200 hover:shadow-lg transition">
    <div class="aspect-[4/3] overflow-hidden bg-gray-100">
        <img src="{{ $img }}" alt="{{ $name }}"
            loading="lazy" decoding="async" fetchpriority="low"
            onerror="this.onerror=null;this.src='https://picsum.photos/seed/{{ md5($name) }}/640/480';"
            class="h-full w-full object-cover group-hover:scale-105 transition duration-300">
    </div>

    <div class="p-4">
        <h3 class="font-semibold line-clamp-2">
            @if($url)
                <a href="{{ $url }}" class="hover:underline">{{ $name }}</a>
            @else
                {{ $name }}
            @endif
        </h3>

        <div class="mt-3 flex items-center justify-between">
            <span class="text-lg font-bold text-gray-900">
                R$ {{ number_format($price, 2, ',', '.') }}
            </span>

            @if($inStock && $model)
                <!-- Form de adicionar ao carrinho -->
                <form method="POST" action="{{ route('cart.add', $model) }}">
                    @csrf
                    <input type="hidden" name="qty" value="1">
                    <button
                        class="inline-flex items-center rounded-lg bg-pink-600 px-3 py-2 text-sm font-semibold text-white hover:bg-pink-700">
                        Adicionar
                    </button>
                </form>
            @elseif(!$inStock)
                <span class="text-xs rounded-full bg-gray-100 text-gray-700 px-2 py-1">Esgotado</span>
            @else
                <!-- Placeholder (sem carrinho ainda) -->
                <button type="button" disabled
                    class="inline-flex items-center rounded-lg bg-gray-200 px-3 py-2 text-sm font-semibold text-gray-500 cursor-not-allowed">
                    Indisponível
                </button>
            @endif
        </div>
    </div>
</article>
