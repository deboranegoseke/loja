<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Novo endereço</h2>
            <h6>resources\views\enderecos\create.blade.php</h6>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('enderecos.store') }}">
                    @csrf
                    @include('enderecos._form', ['submitLabel' => 'Cadastrar'])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
