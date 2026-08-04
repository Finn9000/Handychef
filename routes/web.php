<?php

use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\GhostKitchenController as AdminGhostKitchenController;
use App\Http\Controllers\Admin\MealPlanController as AdminMealPlanController;
use App\Http\Controllers\Admin\PickupController as AdminPickupController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Customer\MealPlanController as CustomerMealPlanController;
use App\Http\Controllers\Customer\SubscriptionController as CustomerSubscriptionController;
use App\Http\Controllers\GhostKitchen\MealItemController;
use App\Http\Controllers\GhostKitchen\MealPlanController;
use App\Http\Controllers\GhostKitchen\PickupController;
use App\Http\Controllers\GhostKitchen\SubscriberController;
use App\Http\Controllers\ProfileController;
use App\Models\GhostKitchen;
use App\Models\MealPlan as MealPlanModel;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Smart redirector — sends the user to their role's dashboard after login.
Route::get('/dashboard', function () {
    $user = auth()->user();

    return match ($user->role) {
        User::ROLE_ADMIN => redirect()->route('admin.dashboard'),
        User::ROLE_GHOST_KITCHEN => redirect()->route('kitchen.dashboard'),
        default => redirect()->route('customer.dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Customer routes
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', function () {
        $activeSubscriptionsCount = auth()->user()->subscriptions()->where('status', 'active')->count();

        return view('customer.dashboard', compact('activeSubscriptionsCount'));
    })->name('dashboard');

    Route::get('meal-plans', [CustomerMealPlanController::class, 'index'])->name('meal-plans.index');
    Route::get('meal-plans/{mealPlan}', [CustomerMealPlanController::class, 'show'])->name('meal-plans.show');

    Route::post('meal-plans/{mealPlan}/subscribe', [CustomerSubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::get('subscription', [CustomerSubscriptionController::class, 'show'])->name('subscription.show');
    Route::delete('subscription/{subscription}', [CustomerSubscriptionController::class, 'destroy'])->name('subscription.destroy');
    Route::post('notifications/{notification}/dismiss', [CustomerSubscriptionController::class, 'dismissNotification'])->name('notifications.dismiss');
});

// Ghost Kitchen routes
Route::middleware(['auth', 'role:ghost_kitchen'])->prefix('kitchen')->name('kitchen.')->group(function () {
    Route::get('/pending', function () {
        return view('ghost-kitchen.pending');
    })->name('pending');

    Route::middleware('kitchen.approved')->group(function () {
        Route::get('/dashboard', function () {
            return view('ghost-kitchen.dashboard');
        })->name('dashboard');

        Route::resource('meal-plans', MealPlanController::class)->except(['show']);

        Route::get('meal-plans/{mealPlan}/items', [MealItemController::class, 'index'])->name('meal-plans.items.index');
        Route::post('meal-plans/{mealPlan}/items', [MealItemController::class, 'store'])->name('meal-plans.items.store');
        Route::patch('meal-plans/{mealPlan}/items/{mealItem}', [MealItemController::class, 'update'])->name('meal-plans.items.update');
        Route::delete('meal-plans/{mealPlan}/items/{mealItem}', [MealItemController::class, 'destroy'])->name('meal-plans.items.destroy');

        Route::get('subscribers', [SubscriberController::class, 'index'])->name('subscribers.index');

        Route::get('pickups', [PickupController::class, 'index'])->name('pickups.index');
        Route::post('pickups/{pickup}/prepared', [PickupController::class, 'markPrepared'])->name('pickups.prepared');
    });
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        $stats = [
            'customers' => User::where('role', User::ROLE_CUSTOMER)->count(),
            'kitchens' => GhostKitchen::count(),
            'pendingKitchens' => GhostKitchen::where('status', 'pending')->count(),
            'mealPlans' => MealPlanModel::count(),
            'activeSubscriptions' => \App\Models\Subscription::where('status', 'active')->count(),
            'pickupsAwaiting' => \App\Models\PickupSchedule::where('status', 'prepared')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    })->name('dashboard');

    Route::get('customers', [AdminCustomerController::class, 'index'])->name('customers.index');
    Route::delete('customers/{customer}', [AdminCustomerController::class, 'destroy'])->name('customers.destroy');

    Route::get('kitchens', [AdminGhostKitchenController::class, 'index'])->name('kitchens.index');
    Route::post('kitchens/{kitchen}/approve', [AdminGhostKitchenController::class, 'approve'])->name('kitchens.approve');
    Route::delete('kitchens/{kitchen}', [AdminGhostKitchenController::class, 'destroy'])->name('kitchens.destroy');

    Route::get('meal-plans', [AdminMealPlanController::class, 'index'])->name('meal-plans.index');
    Route::post('meal-plans/{mealPlan}/toggle', [AdminMealPlanController::class, 'toggle'])->name('meal-plans.toggle');
    Route::delete('meal-plans/{mealPlan}', [AdminMealPlanController::class, 'destroy'])->name('meal-plans.destroy');

    Route::get('pickups', [AdminPickupController::class, 'index'])->name('pickups.index');
    Route::post('pickups/{pickup}/notify', [AdminPickupController::class, 'notify'])->name('pickups.notify');
    Route::post('pickups/{pickup}/collected', [AdminPickupController::class, 'markCollected'])->name('pickups.collected');

    Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');
});

require __DIR__.'/auth.php';
