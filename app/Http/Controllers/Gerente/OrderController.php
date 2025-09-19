<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $q           = trim((string) $request->input('q'));
        $status      = $request->input('status');           // pending|paid|cancelled
        $fulfillment = $request->input('fulfillment');      // separacao|em_transito|rota_entrega|entregue|...

        $orders = Order::query()
            ->with(['user'])
            ->when($q, function ($qry) use ($q) {
                $qry->where(function ($sub) use ($q) {
                    $sub->where('id', (int) $q)
                        ->orWhere('customer_name', 'like', "%{$q}%")
                        ->orWhere('customer_email', 'like', "%{$q}%")
                        ->orWhere('tracking_code', 'like', "%{$q}%");
                });
            })
            ->when($status, fn($qry) => $qry->where('status', $status))
            ->when($fulfillment, fn($qry) => $qry->where('fulfillment_status', $fulfillment))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // opções para selects
        $paymentStatuses = [
            'pending'   => 'Pendente',
            'paid'      => 'Pago',
            'cancelled' => 'Cancelado',
        ];

        $fulfillmentStatuses = [
            'aguardando'  => 'Aguardando',
            'separacao'    => 'Separação',
            'em_transito'  => 'Em trânsito',
            'entregue'     => 'Entregue',
            'problema'     => 'Ocorrência',
            'cancelado'    => 'Cancelado',
        ];

        return view('gerente.orders.index', compact('orders','paymentStatuses','fulfillmentStatuses','q','status','fulfillment'));
    }

    public function update(Request $request, Order $order)
    {
        // valida apenas os campos permitidos
        $data = $request->validate([
            'status'             => 'nullable|in:pending,paid,cancelled',
            'fulfillment_status' => 'required|in:aguardando,separacao,em_transito,rota_entrega,entregue,problema,cancelado',
            'tracking_code'      => 'nullable|string|max:60',
        ]);

        // regra: rastreio só aparece se pago; ainda assim permitimos o gerente ajustar,
        // mas se marcar "pending" forçamos fulfillment a "aguardando"
        if (($data['status'] ?? $order->status) !== 'paid') {
            $data['fulfillment_status'] = 'aguardando';
        }

        $order->update($data);

        return back()->with('status', "Pedido #{$order->id} atualizado.");
    }
}
