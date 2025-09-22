{{-- resources/views/customer/sac/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start sm:items-center justify-between gap-3">
            <div class="min-w-0">
                <h2 class="truncate font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
                    Pedido #{{ $ticket->order_id }}
                    <span class="ml-1 text-gray-400 text-sm">· Protocolo #{{ $ticket->id }}</span>
                </h2>
                <h6 class="mt-0.5 text-[10px] sm:text-xs text-gray-400">
                    resources/views/customer/sac/show.blade.php
                </h6>

                @php
                    $statusNorm = strtolower((string) $ticket->status);
                    $badge = match($statusNorm) {
                        'open','aberto'       => 'bg-yellow-100 text-yellow-800',
                        'closed','fechado'    => 'bg-green-100 text-green-800',
                        'answered','respondido' => 'bg-blue-100 text-blue-800',
                        default               => 'bg-gray-100 text-gray-800'
                    };
                    $statusLabel = match($statusNorm) {
                        'open','aberto'          => 'Aberto',
                        'closed','fechado'       => 'Encerrado',
                        'answered','respondido'  => 'Respondido',
                        default                  => ucfirst($ticket->status ?? '—')
                    };
                @endphp

                <div class="mt-2 flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs {{ $badge }}">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>

            @if(!empty($isStaff) && $isStaff && in_array($statusNorm, ['open','aberto','answered','respondido']))
                <form method="POST" action="{{ route('cliente.sac.close', $ticket) }}" class="w-full sm:w-auto">
                    @csrf
                    <button
                        type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-900">
                        Encerrar chamado
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-8">
            <div
                class="bg-white shadow-sm sm:rounded-lg"
                x-data="{
                    autoscroll() {
                        const box = this.$refs.thread;
                        if (!box) return;
                        box.scrollTop = box.scrollHeight;
                    },
                    autoresize(e) {
                        const el = e?.target ?? this.$refs.msg;
                        if (!el) return;
                        el.style.height = 'auto';
                        el.style.height = Math.min(el.scrollHeight, 240) + 'px';
                    },
                    init() {
                        this.$nextTick(() => this.autoscroll());
                        const ro = new ResizeObserver(() => this.autoscroll());
                        ro.observe(this.$refs.thread);
                    }
                }"
                x-init="init()"
            >
                {{-- Thread --}}
                <div class="p-4 sm:p-6">
                    <div
                        x-ref="thread"
                        class="max-h-[60vh] overflow-y-auto pr-1 sm:pr-2"
                    >
                        <div class="space-y-3">
                            @forelse ($ticket->messages as $m)
                                @php
                                    $isCustomer = ($m->sender_type === 'customer');
                                    $align   = $isCustomer ? 'justify-end' : 'justify-start';
                                    $bubble  = $isCustomer ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-800';
                                    $name    = $m->user->name ?? ($m->sender_type === 'store' ? 'Loja' : 'Cliente');
                                @endphp

                                <div class="flex {{ $align }}">
                                    <div class="max-w-[85%] sm:max-w-[70%]">
                                        <div class="text-[11px] text-gray-500 mb-1">
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
                    </div>
                </div>

                {{-- Responder (somente se não encerrado) --}}
                @if (!in_array($statusNorm, ['closed','fechado']))
                    <div class="px-4 pb-4 sm:px-6 sm:pb-6 border-t">
                        <form method="POST" action="{{ route('cliente.sac.reply', $ticket) }}" class="grid gap-3">
                            @csrf
                            <label for="message" class="block text-sm font-medium text-gray-700">Mensagem</label>
                            <textarea
                                id="message"
                                name="message"
                                rows="3"
                                placeholder="Digite sua mensagem..."
                                class="w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                x-ref="msg"
                                x-on:input="autoresize($event)"
                                required
                            >{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <div class="flex flex-col xs:flex-row xs:items-center gap-2">
                                <button type="submit"
                                        class="w-full xs:w-auto inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                    Enviar
                                </button>

                                <a href="{{ route('cliente.sac.index') }}"
                                   class="w-full xs:w-auto inline-flex items-center justify-center rounded-md border px-4 py-2 text-sm font-medium hover:bg-gray-50">
                                   Voltar aos chamados
                                </a>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="px-4 pb-4 sm:px-6 sm:pb-6 border-t">
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

            {{-- Ações rápidas (mobile) --}}
            <div class="mt-3 flex sm:hidden flex-col gap-2">
                <a href="{{ route('cliente.sac.index') }}"
                   class="inline-flex justify-center rounded-md border px-4 py-2 text-sm font-medium hover:bg-gray-50">
                   Lista de chamados
                </a>
                <a href="{{ route('cliente.pedidos.show', $ticket->order_id) }}"
                   class="inline-flex justify-center rounded-md border px-4 py-2 text-sm font-medium hover:bg-gray-50">
                   Ver pedido
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
