<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar produto resources\views\adm\produtos\edit.blade.php
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                {{-- Mensagens de feedback --}}
                @if (session('success') || session('status'))
                    <div class="mb-4 rounded-lg bg-green-50 text-green-800 px-4 py-3 border border-green-200">
                        {{ session('success') ?? session('status') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 rounded-lg bg-red-50 text-red-800 px-4 py-3 border border-red-200">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-lg bg-red-50 text-red-800 px-4 py-3 border border-red-200">
                        <ul class="list-disc ms-5">
                            @foreach ($errors->all() as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                {{-- /Mensagens de feedback --}}

                <form method="POST" action="{{ route('adm.produtos.update', $product) }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    @include('adm.produtos._form', ['product' => $product])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
