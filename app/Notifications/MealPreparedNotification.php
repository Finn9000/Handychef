<?php

namespace App\Notifications;

use App\Models\PickupSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MealPreparedNotification extends Notification
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
            ->subject('A kitchen has meals ready to route')
            ->line($subscription->mealPlan->ghostKitchen->business_name.' has finished preparing meals for '.$subscription->user->name.'.')
            ->line('Meal plan: '.$subscription->mealPlan->name)
            ->line('Requested pickup time: '.($subscription->pickup_time ?? 'Not specified'))
            ->line('Requested pickup location: '.($subscription->pickup_location ?? 'Not specified'))
            ->line('Once the food reaches that location, notify the customer from the admin Pickups page.');
    }

    public function toArray(object $notifiable): array
    {
        $subscription = $this->pickup->subscription;

        return [
            'pickup_id' => $this->pickup->id,
            'kitchen_name' => $subscription->mealPlan->ghostKitchen->business_name,
            'customer_name' => $subscription->user->name,
            'meal_plan_name' => $subscription->mealPlan->name,
        ];
    }
}
