<?php

namespace App\Http\Controllers\GhostKitchen;

use App\Http\Controllers\Controller;
use App\Models\MealPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MealItemController extends Controller
{
    private function authorizeOwnership(MealPlan $mealPlan): void
    {
        $kitchen = auth()->user()->ghostKitchen()->firstOrFail();

        if ($mealPlan->ghost_kitchen_id !== $kitchen->id) {
            abort(403, 'This meal plan does not belong to you.');
        }
    }

    public function index(MealPlan $mealPlan): View
    {
        $this->authorizeOwnership($mealPlan);

        $mealItems = $mealPlan->mealItems()->latest()->get();

        return view('ghost-kitchen.meal-plans.items', compact('mealPlan', 'mealItems'));
    }

    public function store(Request $request, MealPlan $mealPlan): RedirectResponse
    {
        $this->authorizeOwnership($mealPlan);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $validated['is_available'] = $request->boolean('is_available', true);
        $validated['image_path'] = $request->hasFile('image')
            ? $request->file('image')->store('meal-items', 'public')
            : null;
        unset($validated['image']);

        $mealPlan->mealItems()->create($validated);
        $mealPlan->refreshPrice();

        return redirect()->route('kitchen.meal-plans.items.index', $mealPlan)->with('status', 'Meal item added.');
    }

    public function update(Request $request, MealPlan $mealPlan, \App\Models\MealItem $mealItem): RedirectResponse
    {
        $this->authorizeOwnership($mealPlan);

        if ($mealItem->meal_plan_id !== $mealPlan->id) {
            abort(403);
        }

        $validated = $request->validate([
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('meal-items', 'public');
        }
        unset($validated['image']);

        $mealItem->update($validated);
        $mealPlan->refreshPrice();

        return redirect()->route('kitchen.meal-plans.items.index', $mealPlan)->with('status', 'Meal price updated.');
    }

    public function destroy(MealPlan $mealPlan, \App\Models\MealItem $mealItem): RedirectResponse
    {
        $this->authorizeOwnership($mealPlan);

        if ($mealItem->meal_plan_id !== $mealPlan->id) {
            abort(403);
        }

        $mealItem->delete();
        $mealPlan->refreshPrice();

        return redirect()->route('kitchen.meal-plans.items.index', $mealPlan)->with('status', 'Meal item removed.');
    }
}
