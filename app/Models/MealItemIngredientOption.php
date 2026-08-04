<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['meal_item_id', 'name', 'type', 'price_delta'])]
class MealItemIngredientOption extends Model
{
    use HasFactory;

    public const TYPE_ADD = 'add';
    public const TYPE_REMOVE = 'remove';

    protected function casts(): array
    {
        return [
            'price_delta' => 'decimal:2',
        ];
    }

    public function mealItem()
    {
        return $this->belongsTo(MealItem::class);
    }
}
