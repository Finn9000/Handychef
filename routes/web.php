<?php

use App\Http\Controllers\Customer\MealPlanController as CustomerMealPlanController;
use App\Http\Controllers\Customer\SubscriptionController as CustomerSubscriptionController;
use App\Http\Controllers\GhostKitchen\MealItemController;
use App\Http\Controllers\GhostKitchen\MealItemIngredientOptionController;
use App\Http\Controllers\GhostKitchen\MealPlanController;
use App\Http\Controllers\GhostKitchen\PickupController;
use App\Http\Controllers\GhostKitchen\SubscriberController;
use App\Http\Controllers\ProfileController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Smart redirector — sends the user to their role's dashboard after login.
Route::get('/dashboard', function () {
    $user = auth()->user();

    return match ($user->role) {
        User::ROLE_GHOST_KITCHEN => redirect()->route('kitchen.dashboard'),
        default => redirect()->route('customer.dashboard'),
    };
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Customer routes
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', function () {
        $activeSubscriptions = auth()->user()->subscriptions()
            ->with('mealPlan.ghostKitchen')
            ->where('status', 'active')
            ->latest()
            ->get();

        return view('customer.dashboard', [
            'activeSubscriptionsCount' => $activeSubscriptions->count(),
            'activeSubscriptions' => $activeSubscriptions,
        ]);
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
    Route::get('/dashboard', function () {
        return view('ghost-kitchen.dashboard');
    })->name('dashboard');

    Route::resource('meal-plans', MealPlanController::class)->except(['show']);

    Route::get('meal-plans/{mealPlan}/items', [MealItemController::class, 'index'])->name('meal-plans.items.index');
    Route::post('meal-plans/{mealPlan}/items', [MealItemController::class, 'store'])->name('meal-plans.items.store');
    Route::patch('meal-plans/{mealPlan}/items/{mealItem}', [MealItemController::class, 'update'])->name('meal-plans.items.update');
    Route::delete('meal-plans/{mealPlan}/items/{mealItem}', [MealItemController::class, 'destroy'])->name('meal-plans.items.destroy');

    Route::post('meal-plans/{mealPlan}/items/{mealItem}/ingredient-options', [MealItemIngredientOptionController::class, 'store'])->name('meal-plans.items.ingredient-options.store');
    Route::delete('meal-plans/{mealPlan}/items/{mealItem}/ingredient-options/{ingredientOption}', [MealItemIngredientOptionController::class, 'destroy'])->name('meal-plans.items.ingredient-options.destroy');

    Route::get('subscribers', [SubscriberController::class, 'index'])->name('subscribers.index');

    Route::get('pickups', [PickupController::class, 'index'])->name('pickups.index');
    Route::post('pickups/{pickup}/ready', [PickupController::class, 'markReady'])->name('pickups.ready');
    Route::post('pickups/{pickup}/collected', [PickupController::class, 'markCollected'])->name('pickups.collected');
});

require __DIR__.'/auth.php';
