<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Lista os pedidos do cliente autenticado, incluindo o último ticket (SAC) de cada pedido.
     */
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['tickets' => function ($query) {
                $query->latest()->limit(1); // Carrega apenas o ticket mais recente
            }])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Cria um novo pedido para o usuário autenticado.
     */
    public function store(Request $request)
    {
        $userId = Auth::id();

        $order = Order::create([
            'user_id' => $userId,
            'total'   => $request->input('total', 0), // Em breve: total real do carrinho
            'status'  => 'novo',
        ]);

        return response()->json($order, 201);
    }

    /**
     * Exibe um pedido específico, garantindo que pertence ao usuário autenticado.
     */
    public function show(Request $request, Order $pedido)
    {
        $this->authorizeView($pedido);

        $pedido->load(['items.product']);

        return view('customer.orders.show', ['order' => $pedido]);
    }

    /**
     * Rastreia um pedido com base no código de rastreio.
     */
    public function track(string $code)
    {
        $userId = Auth::id();

        $order = Order::where('user_id', $userId)
                      ->where('tracking_code', $code)
                      ->firstOrFail();

        return response()->json([
            'tracking_code' => $order->tracking_code,
            'status'        => $order->status,
            'updated_at'    => $order->updated_at,
        ]);
    }

    /**
     * Verifica se o pedido pertence ao usuário autenticado.
     */
    protected function authorizeView(Order $order): void
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }
    }
}
