<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Subscription') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-green-100 text-green-800 rounded-md text-sm">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="p-4 bg-red-100 text-red-800 rounded-md text-sm">{{ session('error') }}</div>
            @endif

            @if ($unreadNotifications->isNotEmpty())
                <div class="space-y-2">
                    @foreach ($unreadNotifications as $notification)
                        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 text-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                @if ($notification->type === \App\Notifications\MealCollectedNotification::class)
                                    <p class="font-medium text-indigo-900">{{ __('Pickup confirmed') }}</p>
                                    <p class="text-indigo-700">
                                        {{ $notification->data['meal_plan_name'] ?? 'Your order' }} — {{ __('picked up.') }}
                                    </p>
                                @else
                                    <p class="font-medium text-indigo-900">{{ __('Your meal is ready!') }}</p>
                                    <p class="text-indigo-700">
                                        {{ $notification->data['meal_plan_name'] ?? 'Your meal' }} —
                                        {{ $notification->data['pickup_time'] ?? 'Not specified' }} at
                                        {{ $notification->data['pickup_location'] ?? 'Not specified' }}
                                    </p>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('customer.notifications.dismiss', $notification->id) }}">
                                @csrf
                                <button type="submit" class="text-indigo-600 hover:text-indigo-900 underline whitespace-nowrap">
                                    {{ __('Dismiss') }}
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($subscriptions->isEmpty())
                <div class="bg-white p-6 rounded-lg shadow-sm text-gray-500 text-sm">
                    {{ __("You don't have any active subscriptions.") }}
                    <a href="{{ route('customer.meal-plans.index') }}" class="text-indigo-600 hover:text-indigo-900 underline">
                        {{ __('Browse meal plans') }}
                    </a>
                </div>
            @else
                @foreach ($subscriptions as $subscription)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-lg text-gray-900">{{ $subscription->mealPlan->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $subscription->mealPlan->ghostKitchen->business_name }}</p>
                    <div class="mt-3 text-sm text-gray-600 space-y-1">
                        <p>{{ __('Started') }} {{ $subscription->started_at->toFormattedDateString() }}</p>
                        <p>{{ __('Pickup time') }}: {{ $subscription->pickup_time ?? 'Not specified' }}</p>
                        <p>{{ __('Pickup location') }}: {{ $subscription->pickup_location ?? 'Not specified' }}</p>
                        @if ($subscription->customization_notes)
                            <p>{{ __('Ingredient requests') }}: {{ $subscription->customization_notes }}</p>
                        @endif
                    </div>

                    <div class="mt-4 border-t border-gray-200 pt-4">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ __('Your chosen meals') }}</h4>
                        @php
                            $slotOrder = ['morning' => 0, 'afternoon' => 1, 'evening' => 2];
                            $orderedItems = $subscription->subscriptionItems->sortBy(fn ($si) => $slotOrder[$si->slot] ?? 99);
                        @endphp
                        <ul class="text-sm text-gray-700 space-y-2">
                            @forelse ($orderedItems as $si)
                                <li>
                                    <span class="font-medium text-gray-900">{{ ucfirst($si->slot ?? 'Meal') }}:</span>
                                    {{ $si->mealItem->name }}
                                    @if ($si->ingredientOptions->isNotEmpty())
                                        <ul class="list-disc list-inside text-xs text-gray-500 ms-4">
                                            @foreach ($si->ingredientOptions as $option)
                                                <li>{{ $option->type === 'add' ? 'Add' : 'Remove' }} {{ $option->name }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @empty
                                <li class="text-gray-500">{{ __('No meals selected.') }}</li>
                            @endforelse
                        </ul>
                    </div>

                    <div class="mt-4 border-t border-gray-200 pt-4">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ __('Pickup schedule') }}</h4>
                        <ul class="text-sm text-gray-700 space-y-1">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'ready' => 'bg-green-100 text-green-800',
                                    'collected' => 'bg-gray-100 text-gray-800',
                                ];
                            @endphp
                            @forelse ($subscription->pickupSchedules as $ps)
                                <li class="flex flex-wrap items-center justify-between gap-2">
                                    <span>{{ $ps->pickup_date->toFormattedDateString() }}</span>
                                    <span class="px-2 py-0.5 text-xs rounded-full {{ $statusColors[$ps->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($ps->status) }}
                                    </span>
                                </li>
                            @empty
                                <li class="text-gray-500">{{ __('No pickups scheduled yet.') }}</li>
                            @endforelse
                        </ul>
                    </div>

                    <form method="POST" action="{{ route('customer.subscription.destroy', $subscription) }}" class="mt-4 border-t border-gray-200 pt-4"
                        onsubmit="return confirm('Cancel your subscription?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-white border border-red-300 rounded-md font-semibold text-xs text-red-600 uppercase tracking-widest hover:bg-red-50">
                            {{ __('Cancel Subscription') }}
                        </button>
                    </form>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</x-app-layout>
