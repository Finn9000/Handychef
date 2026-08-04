<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['subscription_id', 'meal_item_id'])]
class SubscriptionItem extends Model
{
    use HasFactory;

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function mealItem()
    {
        return $this->belongsTo(MealItem::class);
    }
}
