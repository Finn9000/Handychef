<?php

namespace App\Http\Controllers\GhostKitchen;

use App\Http\Controllers\Controller;
use App\Models\PickupSchedule;
use App\Notifications\MealCollectedNotification;
use App\Notifications\MealReadyNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PickupController extends Controller
{
    /**
     * List all pickup schedule entries for this kitchen's subscriptions,
     * most recent first, so the kitchen can see what needs to be marked ready.
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
     * Mark a pickup as ready for collection and notify the customer.
     */
    public function markReady(PickupSchedule $pickup): RedirectResponse
    {
        $kitchen = auth()->user()->ghostKitchen()->firstOrFail();

        if ($pickup->subscription->mealPlan->ghost_kitchen_id !== $kitchen->id) {
            abort(403, 'This pickup does not belong to your kitchen.');
        }

        if ($pickup->status !== PickupSchedule::STATUS_PENDING) {
            return redirect()->route('kitchen.pickups.index')
                ->with('error', 'Only pending pickups can be marked as ready.');
        }

        $pickup->update(['status' => PickupSchedule::STATUS_READY]);

        $pickup->subscription->user->notify(new MealReadyNotification($pickup));

        return redirect()->route('kitchen.pickups.index')->with('status', 'Marked as ready — customer notified.');
    }

    /**
     * Mark a ready meal as collected by the customer and confirm it to them.
     */
    public function markCollected(PickupSchedule $pickup): RedirectResponse
    {
        $kitchen = auth()->user()->ghostKitchen()->firstOrFail();

        if ($pickup->subscription->mealPlan->ghost_kitchen_id !== $kitchen->id) {
            abort(403, 'This pickup does not belong to your kitchen.');
        }

        if ($pickup->status !== PickupSchedule::STATUS_READY) {
            return redirect()->route('kitchen.pickups.index')
                ->with('error', 'Only meals that are ready can be marked as collected.');
        }

        $pickup->update(['status' => PickupSchedule::STATUS_COLLECTED]);

        $pickup->subscription->user->notify(new MealCollectedNotification($pickup));

        return redirect()->route('kitchen.pickups.index')->with('status', 'Marked as collected — customer notified.');
    }
}
