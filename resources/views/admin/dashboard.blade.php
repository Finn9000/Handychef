<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <p class="text-sm text-gray-500">Customers</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['customers'] }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <p class="text-sm text-gray-500">Ghost Kitchens</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['kitchens'] }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <p class="text-sm text-gray-500">Pending Kitchens</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['pendingKitchens'] }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <p class="text-sm text-gray-500">Meal Plans</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['mealPlans'] }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <p class="text-sm text-gray-500">Active Subscriptions</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['activeSubscriptions'] }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <p class="text-sm text-gray-500">Pickups Awaiting Action</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['pickupsAwaiting'] }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-500 mb-4">{{ __('Manage the platform:') }}</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.customers.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        {{ __('Manage Customers') }}
                    </a>
                    <a href="{{ route('admin.kitchens.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        {{ __('Manage Kitchens') }}
                    </a>
                    <a href="{{ route('admin.meal-plans.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        {{ __('Manage Meal Plans') }}
                    </a>
                    <a href="{{ route('admin.pickups.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        {{ __('Manage Pickups') }}
                    </a>
                    <a href="{{ route('admin.reports.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        {{ __('View Reports') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
