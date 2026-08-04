<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pickups') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md text-sm">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-md text-sm">{{ session('error') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if ($pickups->isEmpty())
                    <div class="p-6 text-gray-500 text-sm">{{ __('No pickups scheduled yet.') }}</div>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pickup Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($pickups as $pickup)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $pickup->pickup_date->toFormattedDateString() }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $pickup->subscription->user->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $pickup->subscription->mealPlan->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $pickup->subscription->pickup_time ?? 'Not specified' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $pickup->subscription->pickup_location ?? 'Not specified' }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'prepared' => 'bg-blue-100 text-blue-800',
                                                'ready' => 'bg-green-100 text-green-800',
                                                'collected' => 'bg-gray-100 text-gray-800',
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$pickup->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($pickup->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-right">
                                        @if ($pickup->status === 'pending')
                                            <form method="POST" action="{{ route('kitchen.pickups.prepared', $pickup) }}">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md text-xs font-semibold text-white uppercase tracking-wide bg-indigo-600 hover:bg-indigo-700">
                                                    {{ __('Mark Prepared') }}
                                                </button>
                                            </form>
                                        @endif
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
