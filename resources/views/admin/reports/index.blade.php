<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div>
                <h3 class="font-semibold text-gray-800 mb-4">{{ __('Platform Summary') }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <p class="text-sm text-gray-500">Customers</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $summary['customers'] }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <p class="text-sm text-gray-500">Ghost Kitchens</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $summary['kitchensApproved'] }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $summary['kitchensPending'] }} pending approval</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <p class="text-sm text-gray-500">Meal Plans</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $summary['mealPlansActive'] }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $summary['mealPlansInactive'] }} inactive</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <p class="text-sm text-gray-500">Active Subscriptions</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $summary['activeSubscriptions'] }}</p>
                    </div>
                </div>
                <div class="mt-4 bg-white p-6 rounded-lg shadow-sm">
                    <p class="text-sm text-gray-500">Estimated Weekly Recurring Revenue</p>
                    <p class="text-2xl font-semibold text-gray-900">${{ number_format($summary['weeklyRevenue'], 2) }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ __('Sum of weekly plan prices across all active subscriptions.') }}</p>
                </div>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800 mb-4">{{ __('Revenue by Kitchen') }}</h3>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    @if ($revenueByKitchen->isEmpty())
                        <div class="p-6 text-gray-500 text-sm">{{ __('No kitchens yet.') }}</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kitchen</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Active Subscribers</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Weekly Revenue</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($revenueByKitchen as $row)
                                        <tr>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $row['name'] }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $row['activeSubscribers'] }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">${{ number_format($row['weeklyRevenue'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800 mb-4">{{ __('Subscriptions by Meal Plan') }}</h3>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    @if ($subscriptionsByPlan->isEmpty())
                        <div class="p-6 text-gray-500 text-sm">{{ __('No meal plans yet.') }}</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kitchen</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Active Subscribers</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($subscriptionsByPlan as $plan)
                                        <tr>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $plan->name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $plan->ghostKitchen->business_name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">${{ number_format($plan->price, 2) }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $plan->active_subscriptions_count }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-gray-800 mb-4">{{ __('New Signups (Last 8 Weeks)') }}</h3>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        @if ($signupsByWeek->isEmpty())
                            <div class="p-6 text-gray-500 text-sm">{{ __('No signups in this period.') }}</div>
                        @else
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Week Of</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">New Customers</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($signupsByWeek as $row)
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ \Illuminate\Support\Carbon::parse($row->week_start)->toFormattedDateString() }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $row->total }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-800 mb-4">{{ __('Pickup Status Breakdown') }}</h3>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        @if ($pickupStatusBreakdown->isEmpty())
                            <div class="p-6 text-gray-500 text-sm">{{ __('No pickups yet.') }}</div>
                        @else
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Count</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($pickupStatusBreakdown as $row)
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ ucfirst($row->status) }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $row->total }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
