<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['subscription_id', 'pickup_date', 'status'])]
class PickupSchedule extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_READY = 'ready';
    public const STATUS_COLLECTED = 'collected';

    protected function casts(): array
    {
        return [
            'pickup_date' => 'date',
        ];
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
