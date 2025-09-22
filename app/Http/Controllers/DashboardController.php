<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\SupportTicket; // seu model de SAC

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $hoje = Carbon::today();

        // ===== KPIs base =====
        $receitaHoje = Order::whereDate('created_at', $hoje)->sum('total');

        // Fallback p/ status inexistente em orders
        $tabelaOrders = (new Order())->getTable();
        if (Schema::hasColumn($tabelaOrders, 'status')) {
            $pedidosPendentes = Order::whereIn('status', ['pendente','processando','em_separacao'])->count();
        } else {
            $pedidosPendentes = 0;
        }

        // ===== Produtos (tratar ausência de colunas comuns) =====
        $tabelaProdutos = (new Product())->getTable();

        if (Schema::hasColumn($tabelaProdutos, 'is_active')) {
            $produtosAtivos = Product::where('is_active', 1)->count();
        } elseif (Schema::hasColumn($tabelaProdutos, 'active')) {
            $produtosAtivos = Product::where('active', 1)->count();
        } elseif (Schema::hasColumn($tabelaProdutos, 'status')) {
            $produtosAtivos = Product::whereIn('status', ['ativo','active','disponivel','available'])->count();
        } else {
            $produtosAtivos = Product::count();
        }

        // ===== Últimos pedidos (sempre definir a variável) =====
        try {
            $ultimosPedidos = Order::with(['customer'])->latest()->take(8)->get();
        } catch (\Throwable $e) {
            $ultimosPedidos = Order::latest()->take(8)->get();
        }

        // ===== SAC (SupportTicket) =====
        $temSAC = class_exists(\App\Models\SupportTicket::class);
        $ticketsAbertos = 0;
        $ticketsRecentes = collect();

        if ($temSAC) {
            $tabelaTickets = (new SupportTicket())->getTable();

            if (Schema::hasColumn($tabelaTickets, 'status')) {
                $ticketsAbertos = SupportTicket::whereIn('status', [
                    'aberto','em_analise','pendente','open','pending','in_progress'
                ])->count();
            } else {
                $ticketsAbertos = SupportTicket::count();
            }

            $ticketsRecentes = SupportTicket::latest()->take(6)->get();
        }

        // ===== Usuários (visível p/ gerente) =====
        $user = $request->user();
        $podeVerUsuarios = $user && method_exists($user, 'hasRole') && $user->hasRole('gerente');

        $usuariosRecentes = $podeVerUsuarios
            ? User::latest()->take(5)->get()
            : collect();

        return view('dashboard', [
            'user'              => $user,            // <<-- enviado para a Blade
            'receitaHoje'       => $receitaHoje,
            'pedidosPendentes'  => $pedidosPendentes,
            'produtosAtivos'    => $produtosAtivos,
            'ultimosPedidos'    => $ultimosPedidos,
            'ticketsAbertos'    => $ticketsAbertos,
            'ticketsRecentes'   => $ticketsRecentes,
            'usuariosRecentes'  => $usuariosRecentes,
            'podeVerUsuarios'   => $podeVerUsuarios,
            'temSAC'            => $temSAC,
            'temColunaIsActive' => Schema::hasColumn($tabelaProdutos, 'is_active')
                || Schema::hasColumn($tabelaProdutos, 'active')
                || Schema::hasColumn($tabelaProdutos, 'status'),
        ]);
    }
}
