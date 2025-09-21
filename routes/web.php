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
use App\Http\Controllers\Gerente\ReportController;
use App\Http\Controllers\AddressController;
use App\Models\Product;

// ==========================
// Página inicial (produtos públicos)
// ==========================
Route::get('/', function () {
    $produtos = Product::where('active', true)->latest()->take(10000)->get();
    return view('welcome', compact('produtos'));
});

// ==========================
// Autenticação (Laravel Breeze)
// ==========================
require __DIR__ . '/auth.php';

// ==========================
// Dashboard (somente ADM/GERENTE)
// ==========================
Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified', 'role:adm|gerente'])
    ->name('dashboard');

// ==========================
// Perfil (usuário logado)
// ==========================
Route::middleware('auth')->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');       // ProfileController@edit
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update'); // ProfileController@update
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); // ProfileController@destroy
});

// ==========================
// Áreas protegidas (clientes e staff)
// ==========================
Route::middleware(['auth', 'verified'])->group(function () {

    // CLIENTE + STAFF: pedidos e SAC
    Route::middleware('role:cliente|adm|gerente')->group(function () {
        // Pedidos
        Route::get('/pedidos',            [CustomerOrderController::class, 'index'])->name('cliente.pedidos.index');   // CustomerOrderController@index
        Route::post('/pedidos',           [CustomerOrderController::class, 'store'])->name('cliente.pedidos.store');   // CustomerOrderController@store
        Route::get('/pedidos/{pedido}',   [CustomerOrderController::class, 'show'])->name('cliente.pedidos.show');     // CustomerOrderController@show
        Route::get('/rastreio/{code}',    [CustomerOrderController::class, 'track'])->name('cliente.pedidos.track');   // CustomerOrderController@track

        // SAC (atrelado a pedidos)
        Route::prefix('sac')->name('cliente.sac.')->group(function () {
            Route::get('/',                 [SupportTicketController::class, 'index'])->name('index');             // SupportTicketController@index
            Route::get('/novo/{order}',     [SupportTicketController::class, 'create'])->whereNumber('order')->name('create'); // SupportTicketController@create
            Route::post('/',                [SupportTicketController::class, 'store'])->name('store');             // SupportTicketController@store
            Route::get('/{ticket}',         [SupportTicketController::class, 'show'])->whereNumber('ticket')->name('show'); // SupportTicketController@show
            Route::post('/{ticket}/reply',  [SupportTicketController::class, 'reply'])->whereNumber('ticket')->name('reply'); // SupportTicketController@reply
            Route::post('/{ticket}/close',  [SupportTicketController::class, 'close'])
                ->whereNumber('ticket')->name('close')->middleware('role:gerente|adm'); // apenas gerente/adm pode fechar
        });
    });

    // ADM + GERENTE: catálogo de produtos
    Route::prefix('adm')->as('adm.')->middleware('role:adm|gerente')->group(function () {
        Route::view('/', 'adm.dashboard')->name('dashboard');  // View estática
        Route::resource('produtos', AdminProductController::class) // AdminProductController
            ->parameters(['produtos' => 'produto'])
            ->names('produtos');
    });

    // GERENTE: painel, usuários, pedidos e relatórios
    Route::prefix('gerente')->as('gerente.')->middleware('role:gerente')->group(function () {
        Route::view('/', 'gerente.dashboard')->name('dashboard'); // View estática

        // Usuários
        Route::get('/usuarios',          [ManagerUserController::class, 'index'])->name('usuarios.index');   // ManagerUserController@index
        Route::patch('/usuarios/{user}', [ManagerUserController::class, 'update'])->name('usuarios.update'); // ManagerUserController@update

        // Pedidos
        Route::get('/pedidos',           [ManagerOrderController::class, 'index'])->name('pedidos.index');   // ManagerOrderController@index
        Route::patch('/pedidos/{order}', [ManagerOrderController::class, 'update'])->name('pedidos.update'); // ManagerOrderController@update

        // Relatórios
        Route::prefix('relatorios')->as('relatorios.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index'); // ReportController@index
        });
    });
});

// ==========================
// Produtos públicos
// ==========================
Route::get('/produto/{ref}', [ProductPublicController::class, 'show'])
    ->where('ref', '[A-Za-z0-9\-]+')
    ->name('produto.show'); // ProductPublicController@show

// ==========================
// Carrinho (público)
// ==========================
Route::prefix('carrinho')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');             // CartController@index
    Route::post('/adicionar/{produto}', [CartController::class, 'add'])->name('cart.add');  // CartController@add
    Route::patch('/alterar/{produto}', [CartController::class, 'update'])->name('cart.update'); // CartController@update
    Route::delete('/remover/{produto}', [CartController::class, 'remove'])->name('cart.remove'); // CartController@remove
    Route::post('/limpar', [CartController::class, 'clear'])->name('cart.clear');      // CartController@clear
});

// ==========================
// Endereços (todos usuários logados)
// ==========================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('enderecos', AddressController::class)
        ->parameters(['enderecos' => 'endereco'])
        ->names('enderecos')
        ->except(['show']); // AddressController (CRUD de endereços)
});

// ==========================
// Checkout e Pagamento (somente logado com endereço cadastrado)
// ==========================
Route::middleware(['auth', 'address.required'])->group(function () {
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');   // CheckoutController@store
    Route::get('/pagamento/pix/{order}', [PaymentController::class, 'show'])->name('pix.show'); // PaymentController@show
    Route::post('/pagamento/pix/{order}/confirmar', [PaymentController::class, 'confirm'])->name('pix.confirm'); // PaymentController@confirm
});
