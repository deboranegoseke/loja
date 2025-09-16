@php
    /** @var \App\Models\Address|null $address */
    $address = $address ?? null;
    $old = fn($k, $default='') => old($k, $address->{$k} ?? $default);

    // UFs do Brasil
    $ufs = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];
@endphp

{{-- mensagens de feedback (opcionalmente o wrapper pode exibir também) --}}
@if (session('success'))
    <div class="mb-4 rounded-lg bg-green-50 text-green-800 px-4 py-3 border border-green-200">
        {{ session('success') }}
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
            @foreach ($errors->all() as $m)
                <li>{{ $m }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- se não for staff, força o endereço para o usuário logado --}}
<input type="hidden" name="user_id" value="{{ old('user_id', $address->user_id ?? auth()->id()) }}">

<div class="grid grid-cols-1 md:grid-cols-6 gap-4">
    <div class="md:col-span-4">
        <x-input-label value="Logradouro" />
        <x-text-input name="logradouro" type="text" class="mt-1 block w-full"
                      value="{{ $old('logradouro') }}" required />
        <x-input-error :messages="$errors->get('logradouro')" class="mt-2" />
    </div>

    <div class="md:col-span-2">
        <x-input-label value="Número" />
        <x-text-input name="numero" type="text" class="mt-1 block w-full"
                      value="{{ $old('numero') }}" required />
        <x-input-error :messages="$errors->get('numero')" class="mt-2" />
    </div>

    <div class="md:col-span-3">
        <x-input-label value="Complemento (opcional)" />
        <x-text-input name="complemento" type="text" class="mt-1 block w-full"
                      value="{{ $old('complemento') }}" />
        <x-input-error :messages="$errors->get('complemento')" class="mt-2" />
    </div>

    <div class="md:col-span-3">
        <x-input-label value="Bairro" />
        <x-text-input name="bairro" type="text" class="mt-1 block w-full"
                      value="{{ $old('bairro') }}" required />
        <x-input-error :messages="$errors->get('bairro')" class="mt-2" />
    </div>

    <div class="md:col-span-3">
        <x-input-label value="Cidade" />
        <x-text-input name="cidade" type="text" class="mt-1 block w-full"
                      value="{{ $old('cidade') }}" required />
        <x-input-error :messages="$errors->get('cidade')" class="mt-2" />
    </div>

    <div>
        <x-input-label value="UF" />
        <select name="estado" class="mt-1 w-full rounded-md border-gray-300" required>
            <option value="">Selecione</option>
            @foreach ($ufs as $uf)
                <option value="{{ $uf }}" @selected(strtoupper($old('estado')) === $uf)>{{ $uf }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('estado')" class="mt-2" />
    </div>

    <div class="md:col-span-2">
        <x-input-label value="CEP" />
        <x-text-input name="cep" type="text" class="mt-1 block w-full"
                      value="{{ $old('cep') }}" placeholder="00000-000"
                      pattern="\d{5}-?\d{3}" title="CEP no formato 00000-000" required />
        <x-input-error :messages="$errors->get('cep')" class="mt-2" />
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <x-primary-button>{{ $submitLabel ?? 'Salvar' }}</x-primary-button>

    @if(Route::has('enderecos.index'))
        <a href="{{ route('enderecos.index') }}"
           class="text-sm text-gray-600 hover:text-gray-900">
            Voltar
        </a>
    @endif
</div>
