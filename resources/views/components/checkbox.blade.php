@props([
    'name' => null,
    'id' => null,
    'checked' => false,
    'value' => 1,
])

{{-- Hidden para enviar "0" quando desmarcado --}}
@if($name)
    <input type="hidden" name="{{ $name }}" value="0">
@endif

<input
    type="checkbox"
    @if($name) name="{{ $name }}" @endif
    @if($id) id="{{ $id }}" @endif
    value="{{ $value }}"
    @checked(old($name, $checked))
    {!! $attributes->merge(['class' => 'rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500']) !!}
/>
