<?php

namespace App\Http\Controllers\GhostKitchen;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\View\View;

class SubscriberController extends Controller
{
    /**
     * List every active subscriber across all of this kitchen's meal plans.
     */
    public function index(): View
    {
        $kitchen = auth()->user()->ghostKitchen()->firstOrFail();

        $subscriptions = Subscription::with('user', 'mealPlan')
            ->whereHas('mealPlan', fn ($q) => $q->where('ghost_kitchen_id', $kitchen->id))
            ->where('status', 'active')
            ->latest()
            ->get();

        return view('ghost-kitchen.subscribers.index', compact('subscriptions'));
    }
}
