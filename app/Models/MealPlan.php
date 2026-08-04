<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['ghost_kitchen_id', 'name', 'description', 'image_path', 'use_item_photos', 'price', 'available_days', 'is_active'])]
class MealPlan extends Model
{
    use HasFactory;

    /**
     * Days of the week a kitchen can make this plan available on.
     */
    public const DAYS = [
        'mon' => 'Monday',
        'tue' => 'Tuesday',
        'wed' => 'Wednesday',
        'thu' => 'Thursday',
        'fri' => 'Friday',
        'sat' => 'Saturday',
        'sun' => 'Sunday',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'use_item_photos' => 'boolean',
            'available_days' => 'array',
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

    /** Human-readable label for the plan's available days, e.g. "Monday – Thursday". */
    public function availableDaysLabel(): string
    {
        $days = collect($this->available_days ?? [])
            ->map(fn ($day) => self::DAYS[$day] ?? null)
            ->filter()
            ->values();

        if ($days->isEmpty()) {
            return 'Not specified';
        }

        return $days->count() > 1
            ? $days->first().' – '.$days->last()
            : $days->first();
    }
}
