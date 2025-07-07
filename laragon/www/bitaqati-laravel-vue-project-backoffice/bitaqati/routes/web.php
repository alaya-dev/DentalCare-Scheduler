<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\LanguageController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Client/Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('client.home');

Route::get('/dashboard', function () {
    $user = auth()->user();

    // Rediriger vers l'interface d'administration si l'utilisateur a des privilèges admin
    if ($user->hasAdminPrivileges()) {
        return redirect()->route('admin.dashboard');
    }

    // Rediriger les clients vers l'interface client
    return redirect()->route('client.home');
})->middleware(['auth', 'verified'])->name('dashboard');

// Routes pour le changement de langue
Route::post('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');
Route::get('/language/current', [LanguageController::class, 'current'])->name('language.current');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Client Routes
Route::prefix('client')->name('client.')->group(function () {
    // Public client routes
    Route::get('/templates', function () {
        return Inertia::render('Client/Templates');
    })->name('templates');

    Route::get('/pricing', function () {
        return Inertia::render('Client/Pricing');
    })->name('pricing');

    // Authenticated client routes
    Route::middleware('auth')->group(function () {
        Route::get('/profile', function () {
            return Inertia::render('Client/Profile', [
                'user' => auth()->user(),
                'stats' => [
                    'designs_count' => 0,
                    'orders_count' => 0
                ]
            ]);
        })->name('profile');

        Route::get('/my-designs', function () {
            return Inertia::render('Client/MyDesigns', [
                'designs' => []
            ]);
        })->name('my-designs');

        Route::get('/editor/{template?}', function ($template = null) {
            return Inertia::render('Client/Editor', [
                'template' => $template ? ['id' => $template, 'name' => 'قالب تجريبي'] : null
            ]);
        })->name('editor');

        Route::get('/cart', function () {
            return Inertia::render('Client/Cart', [
                'cartItems' => []
            ]);
        })->name('cart');

        Route::get('/checkout', function () {
            return Inertia::render('Client/Checkout', [
                'orderItems' => [],
                'subtotal' => 0,
                'tax' => 0,
                'total' => 0
            ]);
        })->name('checkout');

        Route::get('/orders', function () {
            return Inertia::render('Client/Orders', [
                'orders' => []
            ]);
        })->name('orders');

        Route::get('/invoices', function () {
            return Inertia::render('Client/Invoices', [
                'invoices' => []
            ]);
        })->name('invoices');
    });
});

// Routes d'administration
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Gestion des utilisateurs
    Route::resource('users', UserManagementController::class);
    
    // Gestion des contacts/réclamations
    Route::resource('contacts', App\Http\Controllers\Admin\ContactController::class)->except(['create', 'store', 'edit']);
});

require __DIR__.'/auth.php';
