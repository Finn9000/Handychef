<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MealPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MealPlanController extends Controller
{
    public function index(): View
    {
        $mealPlans = MealPlan::with('ghostKitchen')
            ->withCount('subscriptions')
            ->latest()
            ->get();

        return view('admin.meal-plans.index', compact('mealPlans'));
    }

    public function toggle(MealPlan $mealPlan): RedirectResponse
    {
        $mealPlan->update(['is_active' => ! $mealPlan->is_active]);

        return redirect()->route('admin.meal-plans.index')->with('status', 'Meal plan status updated.');
    }

    public function destroy(MealPlan $mealPlan): RedirectResponse
    {
        $mealPlan->delete();

        return redirect()->route('admin.meal-plans.index')->with('status', 'Meal plan removed.');
    }
}
