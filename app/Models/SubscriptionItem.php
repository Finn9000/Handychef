<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['subscription_id', 'meal_item_id', 'slot'])]
class SubscriptionItem extends Model
{
    use HasFactory;

    public const SLOT_MORNING = 'morning';
    public const SLOT_AFTERNOON = 'afternoon';
    public const SLOT_EVENING = 'evening';

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function mealItem()
    {
        return $this->belongsTo(MealItem::class);
    }

    public function ingredientOptions()
    {
        return $this->belongsToMany(MealItemIngredientOption::class, 'subscription_item_ingredient_options');
    }
}
