<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['meal_plan_id', 'name', 'description', 'image_path', 'is_available'])]
class MealItem extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_available' => 'boolean',
        ];
    }

    public function mealPlan()
    {
        return $this->belongsTo(MealPlan::class);
    }

    public function subscriptionItems()
    {
        return $this->hasMany(SubscriptionItem::class);
    }

    public function ingredientOptions()
    {
        return $this->hasMany(MealItemIngredientOption::class);
    }
}
