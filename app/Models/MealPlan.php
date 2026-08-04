<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['ghost_kitchen_id', 'name', 'description', 'image_path', 'use_item_photos', 'price', 'meals_per_week', 'is_active'])]
class MealPlan extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'use_item_photos' => 'boolean',
        ];
    }

    public function ghostKitchen()
    {
        return $this->belongsTo(GhostKitchen::class);
    }

    public function mealItems()
    {
        return $this->hasMany(MealItem::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /** Keep the stored weekly plan price equal to its meal-item total. */
    public function refreshPrice(): void
    {
        $this->update([
            'price' => $this->mealItems()->sum('price'),
        ]);
    }
}
