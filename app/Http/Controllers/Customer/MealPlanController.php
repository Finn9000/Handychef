<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MealPlan;
use Illuminate\View\View;

class MealPlanController extends Controller
{
    /**
     * Browse all active meal plans from every ghost kitchen.
     */
    public function index(): View
    {
        $mealPlans = MealPlan::with('ghostKitchen', 'mealItems')
            ->where('is_active', true)
            ->latest()
            ->get();

        return view('customer.meal-plans.index', compact('mealPlans'));
    }

    /**
     * View a single plan's details, including its meal items.
     */
    public function show(MealPlan $mealPlan): View
    {
        $mealPlan->load('ghostKitchen', 'mealItems');

        // A customer may subscribe to several different plans, but not to the
        // same plan more than once while that subscription remains active.
        $hasActiveSubscription = auth()->user()->subscriptions()
            ->where('meal_plan_id', $mealPlan->id)
            ->where('status', 'active')
            ->exists();

        return view('customer.meal-plans.show', compact('mealPlan', 'hasActiveSubscription'));
    }
}
