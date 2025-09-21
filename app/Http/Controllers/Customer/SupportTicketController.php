<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $q = trim((string) $request->input('q'));
        $status = $request->input('status'); // open | answered | closed | null

        $user = $request->user();
        $isStaff = $user->hasRole('gerente') || $user->hasRole('adm');

        $tickets = \App\Models\SupportTicket::query()
            ->with(['user', 'order'])
            // 👉 clientes veem só os próprios; gerente/adm veem todos
            ->when(!$isStaff, fn($qb) => $qb->where('user_id', $user->id))
            ->when($status, fn($qb) => $qb->where('status', $status))
            ->when($q, function ($qb) use ($q) {
                $qb->where(function ($qq) use ($q) {
                    if (preg_match('/^\d+$/', $q)) {
                        $qq->orWhere('id', (int) $q)
                            ->orWhere('order_id', (int) $q);
                    }
                    $qq->orWhereHas('user', function ($uq) use ($q) {
                        $uq->where('name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    });
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('customer.sac.index', compact('tickets'));
    }



    public function create(Request $request, Order $order)
    {
        $user = $request->user();
        $isStaff = $user->hasRole('gerente') || $user->hasRole('adm');

        // Cliente só cria se o pedido for dele; staff pode para qualquer pedido
        if (!$isStaff) {
            abort_unless($order->user_id === $user->id, 403);
        }

        if ($existing = $order->tickets()->where('status', 'open')->latest()->first()) {
            return redirect()->route('cliente.sac.show', $existing);
        }

        return view('customer.sac.create', compact('order', 'isStaff'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $isStaff = $user->hasRole('gerente') || $user->hasRole('adm');

        $data = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'message'  => ['required', 'string', 'max:5000'],
        ]);

        $order = Order::findOrFail($data['order_id']);

        // Cliente só pode abrir para pedido próprio; staff pode para qualquer pedido
        if (!$isStaff) {
            abort_unless($order->user_id === $user->id, 403);
        }

        if ($existing = $order->tickets()->where('status', 'open')->latest()->first()) {
            return redirect()->route('cliente.sac.show', $existing);
        }

        // O ticket SEMPRE pertence ao cliente dono do pedido
        $ticket = SupportTicket::create([
            'user_id'  => $order->user_id,
            'order_id' => $order->id,
            'status'   => 'open',
        ]);

        // Primeira mensagem: marca quem abriu (loja ou cliente)
        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id'           => $user->id,
            'sender_type'       => $isStaff ? 'store' : 'customer',
            'body'              => $data['message'],
        ]);

        return redirect()->route('cliente.sac.show', $ticket)
            ->with('status', 'Chamado criado com sucesso!');
    }

    public function show(Request $request, SupportTicket $ticket)
    {
        $user = $request->user();
        $isStaff = $user->hasRole('gerente') || $user->hasRole('adm');
        if (!$isStaff) {
            abort_unless($ticket->user_id === $user->id, 403);
        }

        $ticket->load([
            'order:id',
            'messages.user:id,name',
        ]);

        return view('customer.sac.show', compact('ticket', 'isStaff'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $user = $request->user();
        $isStaff = $user->hasRole('gerente') || $user->hasRole('adm');
        if (!$isStaff) {
            abort_unless($ticket->user_id === $user->id, 403);
        }
        if ($ticket->status === 'closedinto') {
            abort(403);
        }

        $data = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id'           => $user->id,
            'sender_type'       => $isStaff ? 'store' : 'customer',
            'body'              => $data['message'],
        ]);

        return back()->with('status', 'Mensagem enviada.');
    }

    public function close(Request $request, SupportTicket $ticket)
    {
        $user = $request->user();
        abort_unless($user->hasRole('gerente') || $user->hasRole('adm'), 403);

        $ticket->update([
            'status'    => 'closed',
            'closed_at' => now(),
        ]);

        return back()->with('status', 'Chamado encerrado.');
    }
}
