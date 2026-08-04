<?php

namespace App\Http\Controllers\GhostKitchen;

use App\Http\Controllers\Controller;
use App\Models\MealPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MealPlanController extends Controller
{
    /**
     * Get the ghost kitchen profile for the current user, 404 if none.
     */
    private function kitchen()
    {
        return auth()->user()->ghostKitchen()->firstOrFail();
    }

    /**
     * Abort 403 if the given plan doesn't belong to the current kitchen.
     */
    private function authorizeOwnership(MealPlan $mealPlan): void
    {
        if ($mealPlan->ghost_kitchen_id !== $this->kitchen()->id) {
            abort(403, 'This meal plan does not belong to you.');
        }
    }

    public function index(): View
    {
        $mealPlans = $this->kitchen()->mealPlans()->withCount('mealItems', 'subscriptions')->latest()->get();

        return view('ghost-kitchen.meal-plans.index', compact('mealPlans'));
    }

    public function create(): View
    {
        return view('ghost-kitchen.meal-plans.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'use_item_photos' => ['nullable', 'boolean'],
            'price' => ['required', 'numeric', 'min:0'],
            'available_days' => ['required', 'array', 'min:1'],
            'available_days.*' => [Rule::in(array_keys(MealPlan::DAYS))],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['use_item_photos'] = $request->boolean('use_item_photos');
        $validated['image_path'] = $request->hasFile('image')
            ? $request->file('image')->store('meal-plans', 'public')
            : null;
        unset($validated['image']);

        $this->kitchen()->mealPlans()->create($validated);

        return redirect()->route('kitchen.meal-plans.index')->with('status', 'Meal plan created.');
    }

    public function edit(MealPlan $mealPlan): View
    {
        $this->authorizeOwnership($mealPlan);

        return view('ghost-kitchen.meal-plans.edit', compact('mealPlan'));
    }

    public function update(Request $request, MealPlan $mealPlan): RedirectResponse
    {
        $this->authorizeOwnership($mealPlan);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'use_item_photos' => ['nullable', 'boolean'],
            'price' => ['required', 'numeric', 'min:0'],
            'available_days' => ['required', 'array', 'min:1'],
            'available_days.*' => [Rule::in(array_keys(MealPlan::DAYS))],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['use_item_photos'] = $request->boolean('use_item_photos');

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('meal-plans', 'public');
        }
        unset($validated['image']);

        $mealPlan->update($validated);

        return redirect()->route('kitchen.meal-plans.index')->with('status', 'Meal plan updated.');
    }

    public function destroy(MealPlan $mealPlan): RedirectResponse
    {
        $this->authorizeOwnership($mealPlan);

        $mealPlan->delete();

        return redirect()->route('kitchen.meal-plans.index')->with('status', 'Meal plan deleted.');
    }
}
