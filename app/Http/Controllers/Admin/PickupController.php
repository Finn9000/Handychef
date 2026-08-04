<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PickupSchedule;
use App\Notifications\MealReadyNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PickupController extends Controller
{
    public function index(): View
    {
        $pickups = PickupSchedule::with('subscription.user', 'subscription.mealPlan.ghostKitchen')
            ->orderByRaw("CASE status WHEN 'prepared' THEN 0 WHEN 'ready' THEN 1 WHEN 'pending' THEN 2 ELSE 3 END")
            ->orderBy('pickup_date', 'desc')
            ->get();

        return view('admin.pickups.index', compact('pickups'));
    }

    /**
     * Confirm the food has reached the customer's pickup location and notify them.
     */
    public function notify(PickupSchedule $pickup): RedirectResponse
    {
        if ($pickup->status !== PickupSchedule::STATUS_PREPARED) {
            return redirect()->route('admin.pickups.index')
                ->with('error', 'Only prepared pickups can be routed to the customer.');
        }

        $pickup->update(['status' => PickupSchedule::STATUS_READY]);

        $pickup->subscription->user->notify(new MealReadyNotification($pickup));

        return redirect()->route('admin.pickups.index')->with('status', 'Customer notified.');
    }

    public function markCollected(PickupSchedule $pickup): RedirectResponse
    {
        if ($pickup->status !== PickupSchedule::STATUS_READY) {
            return redirect()->route('admin.pickups.index')
                ->with('error', 'Only ready pickups can be marked as collected.');
        }

        $pickup->update(['status' => PickupSchedule::STATUS_COLLECTED]);

        return redirect()->route('admin.pickups.index')->with('status', 'Marked as collected.');
    }
}
