<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Browse Meal Plans') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-md text-sm">{{ session('error') }}</div>
            @endif

            @if ($mealPlans->isEmpty())
                <div class="bg-white p-6 rounded-lg shadow-sm text-gray-500 text-sm">
                    {{ __('No meal plans available right now. Check back soon!') }}
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($mealPlans as $plan)
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100 p-6 flex flex-col hover:shadow-md transition-shadow">
                            <x-meal-plan-image :plan="$plan" class="mb-4 h-44 w-full" />
                            <h3 class="font-semibold text-lg text-gray-900">{{ $plan->name }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $plan->ghostKitchen->business_name }}</p>
                            <p class="text-sm text-gray-600 mt-3 flex-1">{{ Str::limit($plan->description, 100) }}</p>
                            <div class="mt-4 flex items-center justify-between">
                                <span class="font-semibold text-gray-900">${{ number_format($plan->price, 2) }}/wk</span>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">{{ $plan->meals_per_week }} meals/wk</span>
                            </div>
                            <a href="{{ route('customer.meal-plans.show', $plan) }}"
                                class="mt-4 inline-flex justify-center items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                {{ __('View Plan') }}
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
