<x-app-layout>
    {{-- Cabeçalho simples: só identifica pedido e protocolo --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Pedido #{{ $ticket->order_id }}
                    <span class="text-gray-400 text-sm">· Protocolo #{{ $ticket->id }}</span>
                </h2>
                <h6>  resources\views\customer\sac\show.blade.php</h6>
                <div class="flex items-center gap-2">
                    @php
                        $badge = $ticket->status === 'open'
                            ? 'bg-yellow-100 text-yellow-800'
                            : 'bg-green-100 text-green-800';
                    @endphp
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs {{ $badge }}">
                        {{ $ticket->status === 'open' ? 'Aberto' : 'Encerrado' }}
                    </span>
                </div>
            </div>

            {{-- Ações do staff --}}
            @if(!empty($isStaff) && $isStaff && $ticket->status === 'open')
                <form method="POST" action="{{ route('cliente.sac.close', $ticket) }}">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-900">
                        Encerrar chamado
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                {{-- Thread --}}
                <div class="p-6 space-y-4">
                    @forelse ($ticket->messages as $m)
                        @php
                            $mine = $m->sender_type === 'customer' ? 'justify-end' : 'justify-start';
                            $bubble = $m->sender_type === 'customer'
                                ? 'bg-indigo-600 text-white'
                                : 'bg-gray-100 text-gray-800';
                            $name = $m->user->name ?? ($m->sender_type === 'store' ? 'Loja' : 'Cliente');
                        @endphp

                        <div class="flex {{ $mine }}">
                            <div class="max-w-[80%]">
                                <div class="text-xs text-gray-500 mb-1">
                                    {{ $name }} · {{ $m->created_at?->format('d/m/Y H:i') }}
                                </div>
                                <div class="rounded-2xl px-4 py-3 {{ $bubble }}">
                                    {!! nl2br(e($m->body)) !!}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">Nenhuma mensagem ainda.</p>
                    @endforelse
                </div>

                {{-- Responder (somente se aberto) --}}
                @if ($ticket->status === 'open')
                    <div class="px-6 pb-6">
                        <form method="POST" action="{{ route('cliente.sac.reply', $ticket) }}" class="space-y-3">
                            @csrf
                            <label class="block text-sm font-medium text-gray-700">Mensagem</label>
                            <textarea name="message" rows="4"
                                      class="w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Digite sua mensagem..." required>{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <div class="flex items-center gap-3">
                                <button type="submit"
                                        class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                    Enviar
                                </button>

                                <a href="{{ route('cliente.sac.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                                    Voltar aos chamados
                                </a>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="px-6 pb-6">
                        <div class="rounded-md bg-green-50 p-3 text-sm text-green-800">
                            Este chamado está encerrado.
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('cliente.sac.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                                Voltar aos chamados
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
