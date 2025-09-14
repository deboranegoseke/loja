<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar produto</h2></x-slot>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('adm.produtos.update', $product) }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    @include('adm.produtos._form', ['product' => $product])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
