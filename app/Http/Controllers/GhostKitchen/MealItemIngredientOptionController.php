<?php

namespace App\Http\Controllers\GhostKitchen;

use App\Http\Controllers\Controller;
use App\Models\MealItem;
use App\Models\MealItemIngredientOption;
use App\Models\MealPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MealItemIngredientOptionController extends Controller
{
    private function authorize(MealPlan $mealPlan, MealItem $mealItem): void
    {
        $kitchen = auth()->user()->ghostKitchen()->firstOrFail();

        if ($mealPlan->ghost_kitchen_id !== $kitchen->id || $mealItem->meal_plan_id !== $mealPlan->id) {
            abort(403);
        }
    }

    public function store(Request $request, MealPlan $mealPlan, MealItem $mealItem): RedirectResponse
    {
        $this->authorize($mealPlan, $mealItem);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in([MealItemIngredientOption::TYPE_ADD, MealItemIngredientOption::TYPE_REMOVE])],
            'price_delta' => ['required', 'numeric', 'min:0'],
        ]);

        $mealItem->ingredientOptions()->create($validated);

        return redirect()->route('kitchen.meal-plans.items.index', $mealPlan)->with('status', 'Ingredient option added.');
    }

    public function destroy(MealPlan $mealPlan, MealItem $mealItem, MealItemIngredientOption $ingredientOption): RedirectResponse
    {
        $this->authorize($mealPlan, $mealItem);

        if ($ingredientOption->meal_item_id !== $mealItem->id) {
            abort(403);
        }

        $ingredientOption->delete();

        return redirect()->route('kitchen.meal-plans.items.index', $mealPlan)->with('status', 'Ingredient option removed.');
    }
}
