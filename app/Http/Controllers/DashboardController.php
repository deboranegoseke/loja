<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Dashboard\DashboardMetrics;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $data = DashboardMetrics::compute();

        return view('dashboard', [
            'receitaDia'           => $data->receitaDia,
            'receitaMes'           => $data->receitaMes,
            'qtdProdutosAtivos'    => $data->qtdProdutosAtivos,       // active = 0
            'qtdProdutosEsgotados' => $data->qtdProdutosEsgotados,    // stock ≤ 10 (geral)
            'totalSacAberto'       => $data->totalSacAberto,          // 'open' ou 'aberto'
            'pedidosSeparacao'     => collect($data->pedidosSeparacao),
            'qtdPedidosSeparacao'  => $data->qtdPedidosSeparacao,
            'produtosEsgotados'    => collect($data->produtosEsgotados), // vitrine: active = 0 e stock ≤ 10
        ]);
    }
}
