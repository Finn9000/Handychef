<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MealPlan;
use App\Models\Subscription;
use App\Models\SubscriptionItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    /**
     * The three meal slots a customer can fill when subscribing, and the
     * request field names that carry each slot's chosen item/options.
     */
    private const SLOTS = [
        SubscriptionItem::SLOT_MORNING => ['item' => 'morning_item_id', 'options' => 'morning_option_ids'],
        SubscriptionItem::SLOT_AFTERNOON => ['item' => 'afternoon_item_id', 'options' => 'afternoon_option_ids'],
        SubscriptionItem::SLOT_EVENING => ['item' => 'evening_item_id', 'options' => 'evening_option_ids'],
    ];

    /**
     * Show all of the customer's active subscriptions.
     */
    public function show(): View
    {
        $subscriptions = auth()->user()->subscriptions()
            ->with('mealPlan.ghostKitchen', 'subscriptionItems.mealItem', 'subscriptionItems.ingredientOptions', 'pickupSchedules')
            ->where('status', 'active')
            ->latest()
            ->get();

        $unreadNotifications = auth()->user()->unreadNotifications;

        return view('customer.subscription.show', compact('subscriptions', 'unreadNotifications'));
    }

    /**
     * Mark one of the customer's unread notifications as read.
     */
    public function dismissNotification(string $notification): RedirectResponse
    {
        auth()->user()->unreadNotifications()->where('id', $notification)->first()?->markAsRead();

        return redirect()->route('customer.subscription.show');
    }

    /**
     * Subscribe the customer to a meal plan, picking one meal item per slot
     * (morning/afternoon/evening) plus any ingredient options for each.
     */
    public function store(Request $request, MealPlan $mealPlan): RedirectResponse
    {
        $user = auth()->user();

        if ($user->subscriptions()
            ->where('meal_plan_id', $mealPlan->id)
            ->where('status', 'active')
            ->exists()) {
            return redirect()->route('customer.subscription.show')
                ->with('error', 'You already have an active subscription to this meal plan.');
        }

        $validated = $request->validate([
            'morning_item_id' => ['nullable', 'integer', 'exists:meal_items,id'],
            'afternoon_item_id' => ['nullable', 'integer', 'exists:meal_items,id'],
            'evening_item_id' => ['nullable', 'integer', 'exists:meal_items,id'],
            'morning_option_ids' => ['nullable', 'array'],
            'afternoon_option_ids' => ['nullable', 'array'],
            'evening_option_ids' => ['nullable', 'array'],
            'pickup_time' => ['required', 'string', Rule::in(Subscription::PICKUP_TIME_SLOTS)],
            'pickup_location' => ['required', 'string', 'max:255'],
            'pickup_latitude' => ['required', 'numeric', 'between:-90,90'],
            'pickup_longitude' => ['required', 'numeric', 'between:-180,180'],
            'customization_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if (empty($validated['morning_item_id']) && empty($validated['afternoon_item_id']) && empty($validated['evening_item_id'])) {
            return back()->withErrors(['morning_item_id' => 'Please choose a meal for at least one slot.']);
        }

        $planItems = $mealPlan->mealItems()->with('ingredientOptions')->get()->keyBy('id');

        $subscription = $user->subscriptions()->create([
            'meal_plan_id' => $mealPlan->id,
            'status' => 'active',
            'started_at' => now(),
            'pickup_time' => $validated['pickup_time'],
            'pickup_location' => $validated['pickup_location'],
            'pickup_latitude' => $validated['pickup_latitude'],
            'pickup_longitude' => $validated['pickup_longitude'],
            'customization_notes' => $validated['customization_notes'] ?? null,
        ]);

        foreach (self::SLOTS as $slot => $fields) {
            $itemId = $validated[$fields['item']] ?? null;

            if (! $itemId || ! $planItems->has($itemId)) {
                continue;
            }

            $item = $planItems->get($itemId);

            $subscriptionItem = $subscription->subscriptionItems()->create([
                'meal_item_id' => $itemId,
                'slot' => $slot,
            ]);

            $optionIds = collect($request->input($fields['options'], []))
                ->intersect($item->ingredientOptions->pluck('id'));

            if ($optionIds->isNotEmpty()) {
                $subscriptionItem->ingredientOptions()->attach($optionIds);
            }
        }

        // Seed the first pickup schedule entry for today.
        $subscription->pickupSchedules()->create([
            'pickup_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        return redirect()->route('customer.subscription.show')->with('status', 'Subscribed successfully!');
    }

    /**
     * Cancel one of the customer's active subscriptions.
     */
    public function destroy(Subscription $subscription): RedirectResponse
    {
        if ($subscription->user_id !== auth()->id() || ! $subscription->isActive()) {
            abort(404);
        }

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return redirect()->route('customer.dashboard')->with('status', 'Subscription cancelled.');
    }
}
