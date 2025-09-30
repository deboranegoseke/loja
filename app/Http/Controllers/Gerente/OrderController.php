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
        $fulfillment = $request->input('fulfillment');      // aguardando|separacao|em_transito|rota_entrega|entregue|problema|cancelado

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

        // opções para selects (inclui rota_entrega aqui também)
        $paymentStatuses = [
            'pending'   => 'Pendente',
            'paid'      => 'Pago',
            'cancelled' => 'Cancelado',
        ];

        $fulfillmentStatuses = [
            'aguardando'   => 'Aguardando',
            'separacao'    => 'Separação',
            'em_transito'  => 'Em trânsito',
            //'rota_entrega' => 'Rota de entrega',
            'entregue'     => 'Entregue',
            'problema'     => 'Ocorrência',
            'cancelado'    => 'Cancelado',
        ];

        return view('gerente.orders.index', compact('orders','paymentStatuses','fulfillmentStatuses','q','status','fulfillment'));
    }

    public function update(Request $request, Order $order)
    {
        // Deixe fulfillment_status opcional: vamos aplicar a regra automática
        $data = $request->validate([
            'status'             => 'nullable|in:pending,paid,cancelled',
            'fulfillment_status' => 'nullable|in:aguardando,separacao,em_transito,rota_entrega,entregue,problema,cancelado',
            'tracking_code'      => 'nullable|string|max:60',
        ]);

        // Estado atual e futuro
        $novoStatus       = $data['status']             ?? $order->status;
        $novoFulfillment  = $data['fulfillment_status'] ?? $order->fulfillment_status;

        /**
         * Regras:
         * - Se status = paid e fulfillment atual (ou informado) estiver vazio/aguardando → vai para separacao.
         * - Se status = pending → fulfillment = aguardando.
         * - Se status = cancelled → fulfillment = cancelado.
         * - Se já estiver além de separacao (em_transito, rota_entrega, entregue, problema), NUNCA rebaixa.
         */
        $estagiosAvancados = ['separacao','em_transito','rota_entrega','entregue','problema','cancelado'];

        if ($novoStatus === 'paid') {
            if (!$novoFulfillment || $novoFulfillment === 'aguardando') {
                $novoFulfillment = 'separacao';
            }
            // se já for um estágio mais avançado, mantém como está (não faz downgrade)
        } elseif ($novoStatus === 'pending') {
            // pagamento pendente volta para aguardando
            $novoFulfillment = 'aguardando';
        } elseif ($novoStatus === 'cancelled') {
            // pedido cancelado deve refletir no fulfillment
            $novoFulfillment = 'cancelado';
        }

        // Monta payload final
        $payload = [
            'status'             => $novoStatus,
            'fulfillment_status' => $novoFulfillment,
            'tracking_code'      => $data['tracking_code'] ?? $order->tracking_code,
        ];

        $order->update($payload);

        return back()->with('status', "Pedido #{$order->id} atualizado.");
    }
}
