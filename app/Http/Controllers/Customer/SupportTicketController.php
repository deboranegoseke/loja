<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SupportTicket;
use App\Models\SupportMessage; // <-- apenas uma vez
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $tickets = SupportTicket::with('order:id')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customer.sac.index', compact('tickets'));
    }

    public function create(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        if ($existing = $order->tickets()->where('status', 'open')->latest()->first()) {
            return redirect()->route('cliente.sac.show', $existing);
        }

        return view('customer.sac.create', compact('order'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'order_id' => ['required','exists:orders,id'],
            'message'  => ['required','string','max:5000'],
        ]);

        $order = Order::where('id', $data['order_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($existing = $order->tickets()->where('status', 'open')->latest()->first()) {
            return redirect()->route('cliente.sac.show', $existing);
        }

        $ticket = SupportTicket::create([
            'user_id'  => $request->user()->id,
            'order_id' => $order->id,
            'status'   => 'open',
            
        ]);

        // use o import acima (sem backslash global)
        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id'           => $request->user()->id,
            'sender_type'       => 'customer',
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
        if ($ticket->status === 'closed') {
            abort(403);
        }

        $data = $request->validate([
            'message' => ['required','string','max:5000'],
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
