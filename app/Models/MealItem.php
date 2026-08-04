<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['meal_plan_id', 'name', 'description', 'image_path', 'price', 'is_available'])]
class MealItem extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
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
}
