<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GhostKitchen;
use App\Models\MealPlan;
use App\Models\PickupSchedule;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $activeSubscriptions = Subscription::where('status', 'active')->with('mealPlan')->get();

        $summary = [
            'customers' => User::where('role', User::ROLE_CUSTOMER)->count(),
            'kitchensApproved' => GhostKitchen::where('status', 'approved')->count(),
            'kitchensPending' => GhostKitchen::where('status', 'pending')->count(),
            'mealPlansActive' => MealPlan::where('is_active', true)->count(),
            'mealPlansInactive' => MealPlan::where('is_active', false)->count(),
            'activeSubscriptions' => $activeSubscriptions->count(),
            'weeklyRevenue' => $activeSubscriptions->sum(fn (Subscription $s) => $s->mealPlan->price),
        ];

        $revenueByKitchen = GhostKitchen::with('mealPlans.subscriptions')
            ->get()
            ->map(function (GhostKitchen $kitchen) {
                $activeSubs = $kitchen->mealPlans->flatMap->subscriptions->where('status', 'active');

                return [
                    'name' => $kitchen->business_name,
                    'activeSubscribers' => $activeSubs->count(),
                    'weeklyRevenue' => $activeSubs->sum(fn (Subscription $s) => $kitchen->mealPlans->firstWhere('id', $s->meal_plan_id)->price),
                ];
            })
            ->sortByDesc('weeklyRevenue')
            ->values();

        $subscriptionsByPlan = MealPlan::with('ghostKitchen')
            ->withCount(['subscriptions as active_subscriptions_count' => fn ($q) => $q->where('status', 'active')])
            ->orderByDesc('active_subscriptions_count')
            ->get();

        $signupsByWeek = User::where('role', User::ROLE_CUSTOMER)
            ->where('created_at', '>=', now()->subWeeks(8))
            ->selectRaw("strftime('%Y-%W', created_at) as week, min(created_at) as week_start, count(*) as total")
            ->groupBy('week')
            ->orderBy('week')
            ->get();

        $pickupStatusBreakdown = PickupSchedule::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        return view('admin.reports.index', compact(
            'summary',
            'revenueByKitchen',
            'subscriptionsByPlan',
            'signupsByWeek',
            'pickupStatusBreakdown'
        ));
    }
}
