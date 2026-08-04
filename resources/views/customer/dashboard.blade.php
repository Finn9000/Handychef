<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Customer Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 sm:p-8">
                <h3 class="text-xl font-semibold text-gray-900">{{ __('Welcome back, :name', ['name' => auth()->user()->name]) }}</h3>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Browse meal plans and manage your subscription here.') }}
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('customer.subscription.show') }}"
                    class="bg-white overflow-hidden shadow-sm rounded-lg p-6 hover:shadow-md transition-shadow border border-gray-100 block">
                    <p class="text-sm text-gray-500">{{ __('Active Subscriptions') }}</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $activeSubscriptionsCount }}</p>
                    <p class="mt-2 text-sm text-indigo-600 font-medium">{{ __('View my subscription →') }}</p>
                </a>

                <a href="{{ route('customer.meal-plans.index') }}"
                    class="bg-white overflow-hidden shadow-sm rounded-lg p-6 hover:shadow-md transition-shadow border border-gray-100 block">
                    <p class="text-sm text-gray-500">{{ __('Discover new plans') }}</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ __('Browse Meal Plans') }}</p>
                    <p class="mt-2 text-sm text-indigo-600 font-medium">{{ __('See what\'s available →') }}</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
