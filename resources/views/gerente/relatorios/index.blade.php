{{-- resources/views/gerente/relatorios/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2 sm:gap-3">
            <div class="min-w-0">
                <h2 class="truncate font-semibold text-lg sm:text-xl text-gray-800 leading-tight">Relatórios</h2>
                <h6 class="mt-0.5 text-[10px] sm:text-xs text-gray-500">resources\views\gerente\relatorios\index.blade.php</h6>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto w-full max-w-7xl px-3 sm:px-6 lg:px-8">

            {{-- Filtros --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-5 mb-4 sm:mb-6">
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-6 gap-3">
                    @php
                        $currentYear = (int) date('Y');
                        $startYear   = $currentYear - 5;
                    @endphp

                    <div class="sm:col-span-2">
                        <x-input-label value="Ano" />
                        <select name="year" class="mt-1 w-full rounded-md border-gray-300">
                            @for($y = $currentYear; $y >= $startYear; $y--)
                                <option value="{{ $y }}" @selected(($data['year'] ?? $currentYear) == $y)>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <x-input-label value="Comparar com ano anterior" />
                        <select name="compare" class="mt-1 w-full rounded-md border-gray-300">
                            <option value="1" @selected(($data['compare'] ?? true) == true)>Sim</option>
                            <option value="0" @selected(($data['compare'] ?? true) == false)>Não</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2 flex items-end">
                        <x-primary-button class="w-full sm:w-auto justify-center">Aplicar</x-primary-button>
                    </div>
                </form>
            </div>

            {{-- Linha 1: Vendas / Conversão --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-4 sm:mb-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-4 lg:col-span-2">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold">Vendas por mês (R$)</h3>
                        <span class="text-xs text-gray-500" id="salesSub"></span>
                    </div>
                    <div class="relative w-full h-52 sm:h-60 lg:h-72">
                        <canvas id="chartSales" class="absolute inset-0 w-full h-full"></canvas>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold" id="statusTitle">Conversão de pedidos</h3>
                    </div>
                    <div class="relative w-full h-64">
                        <canvas id="chartStatus" class="absolute inset-0 w-full h-full"></canvas>
                    </div>
                </div>
            </div>

            {{-- Linha 2: Margem / Suporte --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold">Lucro por mês (R$)</h3>
                        <span class="text-xs text-gray-500" id="profitSub"></span>
                    </div>
                    <div class="relative w-full h-60">
                        <canvas id="chartProfit" class="absolute inset-0 w-full h-full"></canvas>
                    </div>
                    <div class="mt-4">
                        <h4 class="text-sm text-gray-600 mb-1">Margem (%) — ano atual</h4>
                        <div class="relative w-full h-36">
                            <canvas id="chartMarginPct" class="absolute inset-0 w-full h-full"></canvas>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold">Suporte por mês</h3>
                        <span class="text-xs text-gray-500">Abertos / Respondidos / Fechados</span>
                    </div>
                    <div class="relative w-full h-64">
                        <canvas id="chartSAC" class="absolute inset-0 w-full h-full"></canvas>
                    </div>
                </div>
            </div>

            {{-- Linha 3: Top Produtos / Top Clientes --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <h3 class="font-semibold mb-3" id="topProductsTitle">Produtos mais vendidos</h3>
                    <div class="relative w-full h-72">
                        <canvas id="chartTopProducts" class="absolute inset-0 w-full h-full"></canvas>
                    </div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <h3 class="font-semibold mb-3" id="topCustomersTitle">Clientes que mais compram</h3>
                    <div class="relative w-full h-72">
                        <canvas id="chartTopCustomers" class="absolute inset-0 w-full h-full"></canvas>
                    </div>
                </div>
            </div>

            {{-- Linha 4: Clientes --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold">Clientes: cadastrados × compradores × abandonados</h3>
                    <span class="text-xs text-gray-500" id="customersSub"></span>
                </div>
                <div class="relative w-full h-56 sm:h-64">
                    <canvas id="chartCustomers" class="absolute inset-0 w-full h-full"></canvas>
                </div>
            </div>

        </div>
    </div>

    {{-- Dados em JSON (evita @if/@json dentro do JS) --}}
    <script type="application/json" id="reports-data">{!! json_encode($data ?? [], JSON_UNESCAPED_UNICODE) !!}</script>

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // --- Helpers ---
        const fmtBRL = (v) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v ?? 0);
        const fmtInt = (v) => new Intl.NumberFormat('pt-BR').format(v ?? 0);

        // --- Carrega dados do script JSON ---
        const RAW = document.getElementById('reports-data')?.textContent || '{}';
        const DATA = JSON.parse(RAW || '{}');

        const labels   = DATA.monthLabels || ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
        const year     = DATA.year ?? (new Date()).getFullYear();
        const prevYear = DATA.prevYear ?? (year - 1);
        const compare  = !!DATA.compare;

        // Subtítulos dinâmicos (com checagens para não quebrar se faltarem nós)
        const setText = (id, txt) => { const el = document.getElementById(id); if (el) el.textContent = txt; };
        setText('salesSub', compare ? `${prevYear} × ${year}` : '');
        setText('profitSub', compare ? `${prevYear} × ${year}` : '');
        setText('statusTitle', `Conversão de pedidos (${year})`);
        setText('topProductsTitle', `Produtos mais vendidos (${year})`);
        setText('topCustomersTitle', `Clientes que mais compram (${year})`);
        setText('customersSub', `por mês de ${year}`);

        // Opções base para responsividade
        const baseOpts = {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 250 },
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'bottom' }
            }
        };

        // --- 1) Vendas ---
        const salesDatasets = [];
        if (compare) {
            salesDatasets.push({ label: 'Receita ' + prevYear, data: DATA.sales?.revenue_prev || [], borderWidth: 2, tension: .2 });
        }
        salesDatasets.push(
            { label: 'Receita ' + year, data: DATA.sales?.revenue_current || [], borderWidth: 2, tension: .2 },
            { label: '# Pedidos ' + year, data: DATA.sales?.orders_count || [], borderWidth: 1, borderDash:[5,5], yAxisID: 'y1', tension: .2 }
        );

        new Chart(document.getElementById('chartSales'), {
            type: 'line',
            data: { labels, datasets: salesDatasets },
            options: {
                ...baseOpts,
                scales: {
                    y:  { beginAtZero: true, ticks: { callback: (v)=> fmtBRL(v) } },
                    y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false } }
                },
                plugins: {
                    ...baseOpts.plugins,
                    tooltip: {
                        callbacks: {
                            label: (ctx) =>
                                ctx.dataset.label.includes('Receita')
                                    ? `${ctx.dataset.label}: ${fmtBRL(ctx.parsed.y)}`
                                    : `${ctx.dataset.label}: ${fmtInt(ctx.parsed.y)}`
                        }
                    }
                }
            }
        });

        // --- 2) Status donut ---
        new Chart(document.getElementById('chartStatus'), {
            type: 'doughnut',
            data: {
                labels: ['Pagos','Pendentes','Cancelados'],
                datasets: [{
                    data: [
                        DATA.ordersByStatus?.paid ?? 0,
                        DATA.ordersByStatus?.pending ?? 0,
                        DATA.ordersByStatus?.cancelled ?? 0,
                    ]
                }]
            },
            options: {
                ...baseOpts
            }
        });

        // --- 3) Lucro ---
        const profitDatasets = [];
        if (compare) {
            profitDatasets.push({ label: 'Lucro ' + prevYear, data: DATA.margin?.profit_prev || [] });
        }
        profitDatasets.push({ label: 'Lucro ' + year, data: DATA.margin?.profit_current || [] });

        new Chart(document.getElementById('chartProfit'), {
            type: 'bar',
            data: { labels, datasets: profitDatasets },
            options: {
                ...baseOpts,
                scales: { y: { beginAtZero: true, ticks: { callback: (v)=> fmtBRL(v) } } },
                plugins: {
                    ...baseOpts.plugins,
                    tooltip: { callbacks: { label: (ctx)=> `${ctx.dataset.label}: ${fmtBRL(ctx.parsed.y)}` } }
                }
            }
        });

        // 3b) Margem %
        new Chart(document.getElementById('chartMarginPct'), {
            type: 'line',
            data: { labels, datasets: [{ label: 'Margem (%)', data: DATA.margin?.margin_pct || [], borderWidth: 2, tension: .2 }] },
            options: {
                ...baseOpts,
                scales: { y: { beginAtZero: true, ticks: { callback: (v)=> v + ' %' } } }
            }
        });

        // --- 4) Suporte ---
        new Chart(document.getElementById('chartSAC'), {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: 'Abertos',     data: DATA.sac?.open || [] },
                    { label: 'Respondidos', data: DATA.sac?.answered || [] },
                    { label: 'Fechados',    data: DATA.sac?.closed || [] },
                ]
            },
            options: {
                ...baseOpts,
                scales: { y: { beginAtZero: true } }
            }
        });

        // --- 5) Top Produtos ---
        new Chart(document.getElementById('chartTopProducts'), {
            type: 'bar',
            data: {
                labels: DATA.topProducts?.labels || [],
                datasets: [
                    { label: 'Quantidade',    data: DATA.topProducts?.qty || [] },
                    { label: 'Receita (R$)',  data: DATA.topProducts?.revenue || [] }
                ]
            },
            options: {
                ...baseOpts,
                indexAxis: 'y',
                scales: { x: { beginAtZero: true } },
                plugins: {
                    ...baseOpts.plugins,
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ctx.dataset.label.includes('Receita')
                                ? `${ctx.dataset.label}: ${fmtBRL(ctx.parsed.x)}`
                                : `${ctx.dataset.label}: ${fmtInt(ctx.parsed.x)}`
                        }
                    }
                }
            }
        });

        // --- 6) Top Clientes ---
        new Chart(document.getElementById('chartTopCustomers'), {
            type: 'bar',
            data: {
                labels: DATA.topCustomers?.labels || [],
                datasets: [
                    { label: 'Receita (R$)', data: DATA.topCustomers?.revenue || [] },
                    { label: '# Pedidos',    data: DATA.topCustomers?.orders_count || [] },
                ]
            },
            options: {
                ...baseOpts,
                indexAxis: 'y',
                scales: { x: { beginAtZero: true } },
                plugins: {
                    ...baseOpts.plugins,
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ctx.dataset.label.includes('Receita')
                                ? `${ctx.dataset.label}: ${fmtBRL(ctx.parsed.x)}`
                                : `${ctx.dataset.label}: ${fmtInt(ctx.parsed.x)}`
                        }
                    }
                }
            }
        });

        // --- 7) Clientes ---
        new Chart(document.getElementById('chartCustomers'), {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: 'Cadastrados', data: DATA.customers?.registered || [] },
                    { label: 'Compraram',   data: DATA.customers?.buyers || [] },
                    { label: 'Abandonados', data: DATA.customers?.abandoned || [] },
                ]
            },
            options: {
                ...baseOpts,
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
</x-app-layout>
