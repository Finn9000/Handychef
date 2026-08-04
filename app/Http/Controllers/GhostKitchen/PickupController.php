<?php

namespace App\Http\Controllers\GhostKitchen;

use App\Http\Controllers\Controller;
use App\Models\PickupSchedule;
use App\Models\User;
use App\Notifications\MealPreparedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class PickupController extends Controller
{
    /**
     * List all pickup schedule entries for this kitchen's subscriptions,
     * most recent first, so the kitchen can see what needs to be prepared.
     */
    public function index(): View
    {
        $kitchen = auth()->user()->ghostKitchen()->firstOrFail();

        $pickups = PickupSchedule::with('subscription.user', 'subscription.mealPlan')
            ->whereHas('subscription.mealPlan', fn ($q) => $q->where('ghost_kitchen_id', $kitchen->id))
            ->orderBy('pickup_date', 'desc')
            ->get();

        return view('ghost-kitchen.pickups.index', compact('pickups'));
    }

    /**
     * Mark a pickup as prepared — this hands it off to admin, who routes it to the
     * customer's pickup location and notifies them once it's arrived.
     */
    public function markPrepared(PickupSchedule $pickup): RedirectResponse
    {
        $kitchen = auth()->user()->ghostKitchen()->firstOrFail();

        if ($pickup->subscription->mealPlan->ghost_kitchen_id !== $kitchen->id) {
            abort(403, 'This pickup does not belong to your kitchen.');
        }

        if ($pickup->status !== PickupSchedule::STATUS_PENDING) {
            return redirect()->route('kitchen.pickups.index')
                ->with('error', 'Only pending pickups can be marked as prepared.');
        }

        $pickup->update(['status' => PickupSchedule::STATUS_PREPARED]);

        Notification::send(
            User::where('role', User::ROLE_ADMIN)->get(),
            new MealPreparedNotification($pickup)
        );

        return redirect()->route('kitchen.pickups.index')->with('status', 'Marked as prepared — admin has been notified.');
    }
}
