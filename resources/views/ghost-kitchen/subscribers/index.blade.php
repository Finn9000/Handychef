<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Subscribers') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if ($subscriptions->isEmpty())
                    <div class="p-6 text-gray-500 text-sm">{{ __('No active subscribers yet.') }}</div>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Started</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($subscriptions as $sub)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $sub->user->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $sub->user->email }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $sub->mealPlan->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $sub->started_at->toFormattedDateString() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
