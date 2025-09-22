<?php

namespace App\Models\Dashboard;

/**
 * DTO imutável com os indicadores do Dashboard.
 */
final class DashboardData
{
    public function __construct(
        public readonly float $receitaDia,
        public readonly float $receitaMes,
        public readonly int   $qtdProdutosAtivos,
        public readonly int   $qtdProdutosEsgotados,
        public readonly int   $totalSacAberto,
        public readonly int   $qtdPedidosSeparacao,
        /** @var array<int, array<string, mixed>> */
        public readonly array $pedidosSeparacao,   // lista para tabela
        /** @var array<int, array<string, mixed>> */
        public readonly array $produtosEsgotados   // lista para vitrine
    ) {}
}
