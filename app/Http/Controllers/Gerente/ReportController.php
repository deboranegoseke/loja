<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $year     = (int) $request->input('year', now()->year);
        $compare  = $request->boolean('compare', true);
        $prevYear = $year - 1;

        $monthLabels = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];

        // ======= Descobre colunas reais de order_items =======
        $schema      = DB::getSchemaBuilder();
        $has         = fn($col) => $schema->hasColumn('order_items', $col);

        $priceCol    = $has('price')       ? 'price'
                      : ($has('unit_price') ? 'unit_price' : null);

        $qtyCol      = $has('quantity')    ? 'quantity'
                      : ($has('qty')        ? 'qty'        : null);

        // alguns schemas têm o total da linha já calculado:
        $subtotalCol = $has('subtotal')    ? 'subtotal'
                      : ($has('line_total') ? 'line_total'
                      : ($has('total')      ? 'total'      : null));

        // expressão de receita POR ITEM (sem SUM)
        if ($subtotalCol) {
            $revenuePerItem = "oi.$subtotalCol";
        } elseif ($priceCol && $qtyCol) {
            $revenuePerItem = "oi.$priceCol * oi.$qtyCol";
        } else {
            // fallback: sem colunas confiáveis
            $revenuePerItem = "0";
        }

        // expressão de quantidade (para top produtos / custo)
        $qtyExpr = $qtyCol ? "oi.$qtyCol" : "1";

        // ======= 1) Vendas (orders) =======
        $salesRaw = DB::table('orders')
            ->selectRaw('YEAR(created_at) AS y, MONTH(created_at) AS m, COUNT(*) AS orders_count, SUM(total) AS revenue')
            ->when(!$compare, fn($q) => $q->whereYear('created_at', $year))
            ->when($compare, fn($q) => $q->whereIn(DB::raw('YEAR(created_at)'), [$year, $prevYear]))
            ->where('status', 'paid')
            ->groupBy('y','m')
            ->get();

        $sales = [
            'current' => array_fill(1, 12, 0.0),
            'prev'    => array_fill(1, 12, 0.0),
            'orders'  => array_fill(1, 12, 0),
        ];
        foreach ($salesRaw as $r) {
            if ((int)$r->y === $year) {
                $sales['current'][(int)$r->m] = (float)$r->revenue;
                $sales['orders'][(int)$r->m]  = (int)$r->orders_count;
            } elseif ($compare && (int)$r->y === $prevYear) {
                $sales['prev'][(int)$r->m] = (float)$r->revenue;
            }
        }

        // ======= 2) Margem (order_items + products.cost_price) =======
        $marginRaw = DB::table('order_items AS oi')
            ->join('orders AS o', 'o.id', '=', 'oi.order_id')
            ->leftJoin('products AS p', 'p.id', '=', 'oi.product_id')
            ->selectRaw("
                YEAR(o.created_at) AS y,
                MONTH(o.created_at) AS m,
                SUM($revenuePerItem) AS revenue,
                SUM(COALESCE(p.cost_price,0) * $qtyExpr) AS cost
            ")
            ->when(!$compare, fn($q) => $q->whereYear('o.created_at', $year))
            ->when($compare, fn($q) => $q->whereIn(DB::raw('YEAR(o.created_at)'), [$year, $prevYear]))
            ->where('o.status', 'paid')
            ->groupBy('y','m')
            ->get();

        $margin = [
            'profit_current' => array_fill(1, 12, 0.0),
            'profit_prev'    => array_fill(1, 12, 0.0),
            'margin_pct'     => array_fill(1, 12, 0.0),
        ];
        foreach ($marginRaw as $r) {
            $profit = (float)$r->revenue - (float)$r->cost;
            if ((int)$r->y === $year) {
                $margin['profit_current'][(int)$r->m] = $profit;
                $margin['margin_pct'][(int)$r->m] = ((float)$r->revenue > 0)
                    ? round(($profit / (float)$r->revenue) * 100, 2)
                    : 0.0;
            } elseif ($compare && (int)$r->y === $prevYear) {
                $margin['profit_prev'][(int)$r->m] = $profit;
            }
        }

        // ======= 3) Status do ano =======
        $statusRaw = DB::table('orders')
            ->selectRaw('status, COUNT(*) AS c')
            ->whereYear('created_at', $year)
            ->groupBy('status')
            ->pluck('c','status')
            ->all();

        $ordersByStatus = [
            'paid'      => (int)($statusRaw['paid']      ?? 0),
            'pending'   => (int)($statusRaw['pending']   ?? 0),
            'cancelled' => (int)($statusRaw['cancelled'] ?? 0),
        ];

        // ======= 4) SAC por mês =======
        $sacRaw = DB::table('support_tickets')
            ->selectRaw('MONTH(created_at) AS m, status, COUNT(*) AS c')
            ->whereYear('created_at', $year)
            ->groupBy('m','status')
            ->get();

        $sac = [
            'open'     => array_fill(1, 12, 0),
            'answered' => array_fill(1, 12, 0),
            'closed'   => array_fill(1, 12, 0),
        ];
        foreach ($sacRaw as $r) {
            $sac[$r->status][(int)$r->m] = (int)$r->c;
        }

        // ======= 5) Top produtos (qty e receita) =======
        $revenueSumExpr = "SUM($revenuePerItem)";
        $qtySumExpr     = "SUM($qtyExpr)";

        $topProducts = DB::table('order_items AS oi')
            ->join('orders AS o', 'o.id', '=', 'oi.order_id')
            ->leftJoin('products AS p', 'p.id', '=', 'oi.product_id')
            ->selectRaw("
                COALESCE(p.name, CONCAT('Produto #', oi.product_id)) AS label,
                $qtySumExpr AS qty,
                $revenueSumExpr AS revenue
            ")
            ->where('o.status', 'paid')
            ->whereYear('o.created_at', $year)
            ->groupBy('label')
            ->orderByDesc('qty')
            ->limit(10)
            ->get();

        // ======= 6) Top clientes (orders) =======
        $topCustomers = DB::table('orders AS o')
            ->leftJoin('users AS u', 'u.id', '=', 'o.user_id')
            ->selectRaw('COALESCE(u.name, CONCAT("Cliente #", o.user_id)) AS label, COUNT(*) AS orders_count, SUM(o.total) AS revenue')
            ->where('o.status', 'paid')
            ->whereYear('o.created_at', $year)
            ->groupBy('label')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        // ======= 7) Clientes cadastrados x compradores x abandonados =======
        $registeredRaw = DB::table('users')
            ->selectRaw('MONTH(created_at) AS m, COUNT(*) AS c')
            ->whereYear('created_at', $year)
            ->where('role', 'cliente')
            ->groupBy('m')
            ->pluck('c', 'm')
            ->all();

        $buyersRaw = DB::table('orders')
            ->selectRaw('MONTH(created_at) AS m, COUNT(DISTINCT user_id) AS c')
            ->whereYear('created_at', $year)
            ->where('status', 'paid')
            ->groupBy('m')
            ->pluck('c', 'm')
            ->all();

        $abandoned = array_fill(1, 12, 0);
        $abandonedRaw = DB::table('users AS u')
            ->leftJoin('orders AS o', function ($join) {
                $join->on('o.user_id', '=', 'u.id')->where('o.status', 'paid');
            })
            ->where('u.role', 'cliente')
            ->whereYear('u.created_at', $year)
            ->selectRaw('MONTH(u.created_at) AS m, SUM(CASE WHEN o.id IS NULL THEN 1 ELSE 0 END) AS c')
            ->groupBy('m')
            ->get();
        foreach ($abandonedRaw as $r) {
            $abandoned[(int)$r->m] = (int) $r->c;
        }

        $registered = array_fill(1, 12, 0);
        $buyers     = array_fill(1, 12, 0);
        foreach ($registeredRaw as $m => $c) $registered[(int)$m] = (int)$c;
        foreach ($buyersRaw as $m => $c)     $buyers[(int)$m]     = (int)$c;

        // ======= Pacote para a view =======
        $data = [
            'year'        => $year,
            'prevYear'    => $prevYear,
            'compare'     => $compare,
            'monthLabels' => $monthLabels,

            'sales' => [
                'revenue_current' => array_values($sales['current']),
                'revenue_prev'    => array_values($sales['prev']),
                'orders_count'    => array_values($sales['orders']),
            ],

            'margin' => [
                'profit_current' => array_values($margin['profit_current']),
                'profit_prev'    => array_values($margin['profit_prev']),
                'margin_pct'     => array_values($margin['margin_pct']),
            ],

            'ordersByStatus' => $ordersByStatus,

            'sac' => [
                'open'     => array_values($sac['open']),
                'answered' => array_values($sac['answered']),
                'closed'   => array_values($sac['closed']),
            ],

            'topProducts'  => [
                'labels'  => $topProducts->pluck('label')->toArray(),
                'qty'     => $topProducts->pluck('qty')->map(fn($v)=>(int)$v)->toArray(),
                'revenue' => $topProducts->pluck('revenue')->map(fn($v)=>(float)$v)->toArray(),
            ],

            'topCustomers' => [
                'labels'       => $topCustomers->pluck('label')->toArray(),
                'orders_count' => $topCustomers->pluck('orders_count')->map(fn($v)=>(int)$v)->toArray(),
                'revenue'      => $topCustomers->pluck('revenue')->map(fn($v)=>(float)$v)->toArray(),
            ],

            'customers' => [
                'registered' => array_values($registered),
                'buyers'     => array_values($buyers),
                'abandoned'  => array_values($abandoned),
            ],
        ];

        return view('gerente.relatorios.index', compact('data'));
    }
}
