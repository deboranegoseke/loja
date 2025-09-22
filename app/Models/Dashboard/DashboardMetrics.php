<?php

namespace App\Models\Dashboard;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Modelo dedicado (domínio) para cálculos do Dashboard.
 * Não é Eloquent; usa Query Builder/DB puro.
 */
final class DashboardMetrics
{
    public static function compute(): DashboardData
    {
        $agora     = Carbon::now();
        $hoje      = $agora->copy()->startOfDay();
        $inicioMes = $agora->copy()->startOfMonth();
        $fimMes    = $agora->copy()->endOfMonth();

        // 1) Pedidos em Separação → fulfillment_status = 'separacao'
        $pedidosSeparacao = DB::table('orders')
            ->select([
                'orders.id',
                'orders.total',
                'orders.created_at',
                'orders.customer_name',
                'orders.customer_email',
                'orders.fulfillment_status',
            ])
            ->where('orders.fulfillment_status', 'separacao')
            ->orderByDesc('orders.created_at')
            ->get()
            ->map(function ($row) {
                return [
                    'id'                 => (int)   $row->id,
                    'total'              => (float) ($row->total ?? 0),
                    'created_at'         => $row->created_at ? Carbon::parse($row->created_at) : null,
                    'customer_name'      => $row->customer_name,
                    'customer_email'     => $row->customer_email,
                    'fulfillment_status' => $row->fulfillment_status,
                ];
            })
            ->all();

        $qtdPedidosSeparacao = \count($pedidosSeparacao);

        // 2) Receita do dia (somente pagos)
        $receitaDia = (float) DB::table('orders')
            ->where('status', 'paid')
            ->whereBetween('created_at', [$hoje, $hoje->copy()->endOfDay()])
            ->sum('total');

        // 3) Receita do mês (somente pagos)
        $receitaMes = (float) DB::table('orders')
            ->where('status', 'paid')
            ->whereBetween('created_at', [$inicioMes, $fimMes])
            ->sum('total');

        // 4) Produtos Ativos (card) → regra que você pediu: active = 0
        $qtdProdutosAtivos = (int) DB::table('products')
            ->where('active', 1)
            ->count();

        // 5) Produtos Esgotados (card geral) → stock ≤ 10 (sem filtrar active)
        $qtdProdutosEsgotados = (int) DB::table('products')
            ->where('stock', '<=', 10)
            ->count();

        // 6) Produtos Esgotados na Vitrine (LISTA)
        // Regra: active = 0 OU stock ≤ 10  (mostrar SKU e Nome)
        $produtosEsgotados = DB::table('products')
            ->select(['id', 'sku', 'name', 'stock', 'price', 'active'])
            ->where(function ($q) {
                $q->where('active', 0)
                  ->orWhere('stock', '<=', 10);
            })
            ->orderBy('name')
            ->get()
            ->map(function ($row) {
                $isInactive = (int)($row->active ?? 0) === 0;
                $stock      = (int)($row->stock ?? 0);
                $motivo     = $isInactive && $stock <= 10
                    ? 'inativo & estoque baixo'
                    : ($isInactive
                        ? 'inativo'
                        : ($stock <= 0 ? 'sem estoque' : 'estoque baixo'));

                return [
                    'id'     => (int)   $row->id,
                    'sku'    => $row->sku,
                    'name'   => (string) $row->name,
                    'stock'  => $stock,
                    'price'  => (float) ($row->price ?? 0),
                    'active' => (int)   ($row->active ?? 0),
                    'motivo' => $motivo,
                ];
            })
            ->all();

        // 7) SAC em Aberto → aceitar 'open' e 'aberto' (dump tem os dois padrões)
        $totalSacAberto = Schema::hasTable('support_tickets')
            ? (int) DB::table('support_tickets')
                ->whereIn('status', ['open', 'aberto'])
                ->count()
            : 0;

        return new DashboardData(
            receitaDia:           $receitaDia,
            receitaMes:           $receitaMes,
            qtdProdutosAtivos:    $qtdProdutosAtivos,
            qtdProdutosEsgotados: $qtdProdutosEsgotados,
            totalSacAberto:       $totalSacAberto,
            qtdPedidosSeparacao:  $qtdPedidosSeparacao,
            pedidosSeparacao:     $pedidosSeparacao,
            produtosEsgotados:    $produtosEsgotados
        );
    }
}
