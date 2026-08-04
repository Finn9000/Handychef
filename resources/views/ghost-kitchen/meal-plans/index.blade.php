<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Meal Plans') }}
            </h2>
            <a href="{{ route('kitchen.meal-plans.create') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                {{ __('+ New Plan') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if ($mealPlans->isEmpty())
                    <div class="p-6 text-gray-500 text-sm">
                        {{ __("You haven't created any meal plans yet.") }}
                    </div>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meals/Week</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subscribers</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($mealPlans as $plan)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $plan->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">${{ number_format($plan->price, 2) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $plan->meals_per_week }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $plan->meal_items_count }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $plan->subscriptions_count }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        @if ($plan->is_active)
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-right space-x-3 whitespace-nowrap">
                                        <a href="{{ route('kitchen.meal-plans.items.index', $plan) }}" class="text-indigo-600 hover:text-indigo-900">Items</a>
                                        <a href="{{ route('kitchen.meal-plans.edit', $plan) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <form action="{{ route('kitchen.meal-plans.destroy', $plan) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Delete this meal plan? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md text-xs font-semibold text-white uppercase tracking-wide bg-red-600 hover:bg-red-700">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
