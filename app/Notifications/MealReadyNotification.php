<?php

namespace App\Notifications;

use App\Models\PickupSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MealReadyNotification extends Notification
{
    use Queueable;

    public function __construct(protected PickupSchedule $pickup)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subscription = $this->pickup->subscription;

        return (new MailMessage)
            ->subject('Your meal is ready for pickup!')
            ->line('Your meal for "'.$subscription->mealPlan->name.'" has arrived and is ready for pickup.')
            ->line('Pickup time: '.($subscription->pickup_time ?? 'Not specified'))
            ->line('Pickup location: '.($subscription->pickup_location ?? 'Not specified'))
            ->line('Come collect it whenever you\'re ready!');
    }

    public function toArray(object $notifiable): array
    {
        $subscription = $this->pickup->subscription;

        return [
            'pickup_id' => $this->pickup->id,
            'meal_plan_name' => $subscription->mealPlan->name,
            'pickup_time' => $subscription->pickup_time,
            'pickup_location' => $subscription->pickup_location,
        ];
    }
}
