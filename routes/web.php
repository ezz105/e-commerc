<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Http\Controllers\dashboard\DashboardAuthController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\OrderController;



// Authentication Routes
Route::middleware('guest')->group(function () {
    // Login Routes
    Route::get('/login', [DashboardAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [DashboardAuthController::class, 'login']);

    // Registration Routes
    Route::get('/register', [DashboardAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [DashboardAuthController::class, 'register']);

    // Password Reset Routes (to be implemented)
    Route::get('forgot-password', [DashboardAuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('forgot-password', [DashboardAuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [DashboardAuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [DashboardAuthController::class, 'resetPassword'])->name('password.update');
});

// Authentication Required Routes
Route::middleware('auth')->group(function () {
    // Logout Route
    Route::post('/logout', [DashboardAuthController::class, 'logout'])->name('logout');

    // User Profile Route
    Route::get('/user/profile', [DashboardAuthController::class, 'user'])->name('user.profile');

    // Dashboard Home
    Route::get('/', function () {
        return view('dashboard.home');
    })->name('home');

    // Products Routes
    Route::prefix('products')->group(function () {
        Route::get('/', function () {
            $products = Product::paginate(10);
            return view('dashboard.products.index', compact('products'));
        })->name('products.index');

        Route::get('/create', function () {
            $categories = Category::orderBy('name')->get();
            return view('dashboard.products.create', compact('categories'));
        })->name('products.create');

        Route::get('/{product}', function (Product $product) {
            return view('dashboard.products.show', compact('product'));
        })->name('products.show');

        Route::post('/', function () {
            return redirect()->route('products.index');
        })->name('products.store');

        Route::get('/{product}/edit', function (Product $product) {
            $categories = Category::orderBy('name')->get();
            return view('dashboard.products.create', compact('product',  'categories'));
        })->name('products.edit');

        Route::delete('/{product}', function (Product $product) {
            return redirect()->route('products.index');
        })->name('products.destroy');
    });

    /// users

    Route::prefix('users')->middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/store', [UserController::class, 'store'])->name('users.store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    });



    // Categories Routes
    Route::prefix('categories')->group(function () {
        Route::get('/', function () {
            return "Manage Categories";
        })->name('categories.index');
    });

    // Orders Route
    
    Route::prefix('orders')->middleware(['auth'])->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/{id}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('/{id}', [OrderController::class, 'update'])->name('orders.update');
    });
    
    

    // Analytics Route
    Route::get('/analytics', function () {
        return "View Analytics"; // Replace with view('analytics.index') when implemented
    })->name('analytics.index');

    // Settings Route
    Route::get('/settings', function () {
        return "Settings"; // Replace with view('settings.index') when implemented
    })->name('settings');
});
