<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'business_name', 'manager_name', 'description', 'address', 'phone'])]
class GhostKitchen extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mealPlans()
    {
        return $this->hasMany(MealPlan::class);
    }
}
