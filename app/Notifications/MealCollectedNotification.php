<?php

namespace App\Notifications;

use App\Models\PickupSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MealCollectedNotification extends Notification
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
            ->subject('Pickup confirmed')
            ->line('Your order for "'.$subscription->mealPlan->name.'" has been picked up.')
            ->line('Thanks for using HandyChef!');
    }

    public function toArray(object $notifiable): array
    {
        $subscription = $this->pickup->subscription;

        return [
            'pickup_id' => $this->pickup->id,
            'meal_plan_name' => $subscription->mealPlan->name,
        ];
    }
}
