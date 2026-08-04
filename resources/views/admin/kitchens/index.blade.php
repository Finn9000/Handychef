<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Ghost Kitchens') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md text-sm">{{ session('status') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if ($kitchens->isEmpty())
                    <div class="p-6 text-gray-500 text-sm">{{ __('No ghost kitchens yet.') }}</div>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Business Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Owner Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meal Plans</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($kitchens as $kitchen)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $kitchen->business_name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $kitchen->user->email }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $kitchen->meal_plans_count }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        @if ($kitchen->status === 'approved')
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Approved</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-right space-x-3">
                                        @if ($kitchen->status !== 'approved')
                                            <form action="{{ route('admin.kitchens.approve', $kitchen) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md text-xs font-semibold text-white uppercase tracking-wide bg-green-600 hover:bg-green-700">Approve</button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.kitchens.destroy', $kitchen) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Remove this ghost kitchen and all its meal plans?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md text-xs font-semibold text-white uppercase tracking-wide bg-red-600 hover:bg-red-700">Remove</button>
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
