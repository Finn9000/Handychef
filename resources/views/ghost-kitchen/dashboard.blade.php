<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ghost Kitchen Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("Welcome, ") }}{{ auth()->user()->name }}!
                    <p class="mt-2 text-sm text-gray-500">
                        Manage your meal plans and subscribers here.
                    </p>
                    <a href="{{ route('kitchen.meal-plans.index') }}"
                        class="inline-flex items-center mt-4 px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        {{ __('Manage Meal Plans') }}
                    </a>
                    <a href="{{ route('kitchen.subscribers.index') }}"
                        class="inline-flex items-center mt-4 ms-3 px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                        {{ __('View Subscribers') }}
                    </a>
                    <a href="{{ route('kitchen.pickups.index') }}"
                        class="inline-flex items-center mt-4 ms-3 px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                        {{ __('Pickups') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
