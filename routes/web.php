<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;

// Controllers públicos/cliente
use App\Http\Controllers\Store\ProductPublicController;
use App\Http\Controllers\Store\CartController;
use App\Http\Controllers\Store\CheckoutController;
use App\Http\Controllers\Store\PaymentController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\SupportTicketController;

// Controllers administrativos/gestão
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Gerente\OrderController as ManagerOrderController;
use App\Http\Controllers\Gerente\ReportController;
use App\Http\Controllers\Manager\UserController as ManagerUserController;

// Perfil/conta
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AddressController;

// Dashboard central (ADM + GERENTE)
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Páginas iniciais / Welcome
|--------------------------------------------------------------------------
*/

// Página inicial (home)
Route::get('/', function () {
    // se sua vitrine “ativa” usa active=1, mantenha; se usa active=0, troque para where('active', 0)
    $produtos = Product::where('active', true)->latest()->take(10000)->get();
    return view('welcome', compact('produtos'));
})->name('home');

// Página Welcome para clientes após registro
Route::get('/welcome', function () {
    $produtos = Product::where('active', true)->latest()->take(10000)->get();
    return view('welcome', compact('produtos'));
})->name('welcome');

/*
|--------------------------------------------------------------------------
| Autenticação (Laravel Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Dashboard (APENAS gerente/adm)
| - Não usar Closure aqui. Sempre passar pelo controller para preencher os indicadores.
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:adm|gerente'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Perfil (logado)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Áreas protegidas (cliente + staff)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // CLIENTE + STAFF
    Route::middleware('role:cliente|adm|gerente')->group(function () {

        // Pedidos do cliente
        Route::get('/pedidos',            [CustomerOrderController::class, 'index'])->name('cliente.pedidos.index');
        Route::post('/pedidos',           [CustomerOrderController::class, 'store'])->name('cliente.pedidos.store');
        Route::get('/pedidos/{pedido}',   [CustomerOrderController::class, 'show'])->name('cliente.pedidos.show');
        Route::get('/rastreio/{code}',    [CustomerOrderController::class, 'track'])->name('cliente.pedidos.track');

        // SAC (protegido)
        Route::prefix('sac')->name('cliente.sac.')->group(function () {
            Route::get('/',                 [SupportTicketController::class, 'index'])->name('index');
            Route::get('/novo/{order}',     [SupportTicketController::class, 'create'])->whereNumber('order')->name('create');
            Route::post('/',                [SupportTicketController::class, 'store'])->name('store');
            Route::get('/{ticket}',         [SupportTicketController::class, 'show'])->whereNumber('ticket')->name('show');
            Route::post('/{ticket}/reply',  [SupportTicketController::class, 'reply'])->whereNumber('ticket')->name('reply');

            // Fechar ticket permitido só para gerente|adm
            Route::post('/{ticket}/close',  [SupportTicketController::class, 'close'])
                ->whereNumber('ticket')
                ->middleware('role:gerente|adm')
                ->name('close');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | ADM + GERENTE: catálogo e atalho para o dashboard central
    |--------------------------------------------------------------------------
    */
    Route::prefix('adm')->as('adm.')->middleware(['role:adm|gerente'])->group(function () {
        // Em vez de uma view própria, reaproveita o dashboard central
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('produtos', AdminProductController::class)
            ->parameters(['produtos' => 'produto'])
            ->names('produtos');
    });

    /*
    |--------------------------------------------------------------------------
    | GERENTE: usuários, pedidos, relatórios e atalho para o dashboard central
    |--------------------------------------------------------------------------
    */
    Route::prefix('gerente')->as('gerente.')->middleware(['role:gerente'])->group(function () {
        // Em vez de uma view própria, reaproveita o dashboard central
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Gestão de usuários
        Route::get('/usuarios',          [ManagerUserController::class, 'index'])->name('usuarios.index');
        Route::patch('/usuarios/{user}', [ManagerUserController::class, 'update'])->name('usuarios.update');

        // Gestão de pedidos
        Route::get('/pedidos',           [ManagerOrderController::class, 'index'])->name('pedidos.index');
        Route::patch('/pedidos/{order}', [ManagerOrderController::class, 'update'])->name('pedidos.update');

        // Relatórios
        Route::prefix('relatorios')->as('relatorios.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Produto (público)
|--------------------------------------------------------------------------
*/
Route::get('/produto/{ref}', [ProductPublicController::class, 'show'])
    ->where('ref', '[A-Za-z0-9\-]+')
    ->name('produto.show');

/*
|--------------------------------------------------------------------------
| Carrinho (público)
|--------------------------------------------------------------------------
*/
Route::prefix('carrinho')->group(function () {
    Route::get('/',                   [CartController::class, 'index'])->name('cart.index');
    Route::post('/adicionar/{produto}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/alterar/{produto}',  [CartController::class, 'update'])->name('cart.update');
    Route::delete('/remover/{produto}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/limpar',              [CartController::class, 'clear'])->name('cart.clear');
});

/*
|--------------------------------------------------------------------------
| Checkout e Pagamento (logado)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'address.required'])->group(function () {
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/pagamento/pix/{order}', [PaymentController::class, 'show'])->name('pix.show');
    Route::post('/pagamento/pix/{order}/confirmar', [PaymentController::class, 'confirm'])->name('pix.confirm');
});

/*
|--------------------------------------------------------------------------
| Endereços (todos os perfis, apenas o dono edita/visualiza)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('enderecos', AddressController::class)
        ->parameters(['enderecos' => 'endereco'])
        ->names('enderecos')
        ->except(['show']);
});
