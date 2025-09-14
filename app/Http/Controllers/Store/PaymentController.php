<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\PixPayload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;   // <- use o Facade
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Tela do pagamento via Pix
     */
    public function show(Order $order)
    {
        // Segurança: apenas o dono do pedido pode ver
        $userId = Auth::id();
        if (! Auth::check() || $order->user_id !== $userId) {
            abort(403, 'Pedido não pertence a você.');
        }

        // Permitimos visualizar pending/paid
        if (! in_array($order->status, ['pending', 'paid'], true)) {
            abort(404);
        }

        // Garante TXID
        $txid = $order->pix_txid ?: ('LJ' . Str::upper(Str::random(10)));
        if (! $order->pix_txid) {
            $order->pix_txid = $txid;
        }

        // Gera payload Pix
        $payload = (new PixPayload(
            pixKey:       Config::get('pix.key'),
            merchantName: Config::get('pix.name', config('app.name', 'LOJINHA')),
            merchantCity: Config::get('pix.city', 'SAO PAULO'),
            txid:         $txid,
            amount:       (float) $order->total,
            description:  'Pedido #' . $order->id
        ))->toString();

        // Persiste payload se ainda vazio
        if (! $order->pix_payload) {
            $order->pix_payload = $payload;
        }

        if ($order->isDirty(['pix_txid', 'pix_payload'])) {
            $order->save();
        }

        return view('checkout.pix', [
            'order'   => $order->fresh(['items']),
            'payload' => $order->pix_payload ?: $payload,
        ]);
    }

    /**
     * SIMULA confirmação Pix: marca como 'paid' e baixa estoque.
     */
    public function confirm(Order $order)
    {
        // Segurança: apenas o dono do pedido
        $userId = Auth::id();
        if (! Auth::check() || $order->user_id !== $userId) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return redirect()
                ->route('pix.show', $order)
                ->with('status', 'Pagamento já processado.');
        }

        DB::transaction(function () use ($order) {
            // Marca como pago e inicia rastreio
            $order->update([
                'status' => 'paid',
                'fulfillment_status' => 'separacao'
            ]);

            // Baixa estoque simples
            $items = $order->items()->with('product')->get();
            foreach ($items as $item) {
                if ($item->product) {
                    $newStock = max(0, (int)$item->product->stock - (int)$item->quantity);
                    $item->product->update(['stock' => $newStock]);
                }
            }
        });

        return redirect()
            ->route('pix.show', $order)
            ->with('status', 'Pagamento confirmado! ✔');
    }
}
