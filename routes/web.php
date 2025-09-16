<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\SupportTicketController;
use App\Http\Controllers\Manager\UserController as ManagerUserController;
use App\Http\Controllers\Store\ProductPublicController;
use App\Http\Controllers\Store\CartController;
use App\Http\Controllers\Store\CheckoutController;
use App\Http\Controllers\Store\PaymentController;
use App\Http\Controllers\Gerente\OrderController as ManagerOrderController;
use App\Models\Product;
use App\Http\Controllers\Gerente\ReportController;

// Página inicial (única, com produtos)
Route::get('/', function () {
    $produtos = Product::where('active', true)->latest()->take(12)->get();
    return view('welcome', compact('produtos'));
});

// Autenticação (Laravel Breeze)
require __DIR__ . '/auth.php';

// Dashboard (APENAS gerente/adm)
Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified', 'role:adm|gerente'])
    ->name('dashboard');

// Perfil (logado)
Route::middleware('auth')->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Áreas protegidas
Route::middleware(['auth', 'verified'])->group(function () {

    // CLIENTE + STAFF: Pedidos e SAC (não existe dashboard do cliente)
    Route::middleware('role:cliente|adm|gerente')->group(function () {
        // (Removido) Dashboard do cliente
        // Route::get('/cliente', fn() => response('Área do cliente', 200))->name('cliente.dashboard');

        // Pedidos
        Route::get('/pedidos',            [CustomerOrderController::class, 'index'])->name('cliente.pedidos.index');
        Route::post('/pedidos',           [CustomerOrderController::class, 'store'])->name('cliente.pedidos.store');
        Route::get('/pedidos/{pedido}',   [CustomerOrderController::class, 'show'])->name('cliente.pedidos.show');
        Route::get('/rastreio/{code}',    [CustomerOrderController::class, 'track'])->name('cliente.pedidos.track');

        // SAC (atrelado a pedidos) — protegido
        Route::prefix('sac')->name('cliente.sac.')->group(function () {
            Route::get('/',                 [SupportTicketController::class, 'index'])->name('index');
            Route::get('/novo/{order}',     [SupportTicketController::class, 'create'])->whereNumber('order')->name('create');
            Route::post('/',                [SupportTicketController::class, 'store'])->name('store');
            Route::get('/{ticket}',         [SupportTicketController::class, 'show'])->whereNumber('ticket')->name('show');
            Route::post('/{ticket}/reply',  [SupportTicketController::class, 'reply'])->whereNumber('ticket')->name('reply');
            Route::post('/{ticket}/close',  [SupportTicketController::class, 'close'])
                ->whereNumber('ticket')->name('close')->middleware('role:gerente|adm');
        });
    });

    // ADM + GERENTE: catálogo
    Route::prefix('adm')->as('adm.')->middleware('role:adm|gerente')->group(function () {
        Route::view('/', 'adm.dashboard')->name('dashboard');
        Route::resource('produtos', AdminProductController::class)
            ->parameters(['produtos' => 'produto'])
            ->names('produtos');
    });

    // GERENTE: dashboard, usuários e pedidos
    Route::prefix('gerente')->as('gerente.')->middleware('role:gerente')->group(function () {
        Route::view('/', 'gerente.dashboard')->name('dashboard');

        // Gestão de usuários
        Route::get('/usuarios',          [ManagerUserController::class, 'index'])->name('usuarios.index');
        Route::patch('/usuarios/{user}', [ManagerUserController::class, 'update'])->name('usuarios.update');

        // Gestão de pedidos
        Route::get('/pedidos',           [ManagerOrderController::class, 'index'])->name('pedidos.index');
        Route::patch('/pedidos/{order}', [ManagerOrderController::class, 'update'])->name('pedidos.update');
    });
});

// Produto (público)
Route::get('/produto/{ref}', [ProductPublicController::class, 'show'])
    ->where('ref', '[A-Za-z0-9\-]+')
    ->name('produto.show');

// Carrinho (público)
Route::prefix('carrinho')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/adicionar/{produto}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/alterar/{produto}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/remover/{produto}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/limpar', [CartController::class, 'clear'])->name('cart.clear');
});

// Checkout e Pagamento (logado)
Route::middleware('auth')->group(function () {
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/pagamento/pix/{order}', [PaymentController::class, 'show'])->name('pix.show');
    Route::post('/pagamento/pix/{order}/confirmar', [PaymentController::class, 'confirm'])->name('pix.confirm');
});



Route::prefix('gerente')->as('gerente.')->middleware('role:gerente')->group(function () {
    // ... suas rotas já existentes

    Route::prefix('relatorios')->as('relatorios.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
    });
});
