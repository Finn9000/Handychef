<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'meal_plan_id', 'status', 'started_at', 'cancelled_at', 'pickup_time', 'pickup_location', 'pickup_latitude', 'pickup_longitude', 'customization_notes'])]
class Subscription extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Fixed pickup time slots customers choose from at subscribe time.
     */
    public const PICKUP_TIME_SLOTS = [
        'Morning (9:00 AM - 12:00 PM)',
        'Afternoon (12:00 PM - 4:00 PM)',
        'Evening (4:00 PM - 8:00 PM)',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'cancelled_at' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mealPlan()
    {
        return $this->belongsTo(MealPlan::class);
    }

    public function subscriptionItems()
    {
        return $this->hasMany(SubscriptionItem::class);
    }

    public function pickupSchedules()
    {
        return $this->hasMany(PickupSchedule::class);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}
