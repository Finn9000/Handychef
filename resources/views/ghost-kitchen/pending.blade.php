<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Approval Pending') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                <p class="text-gray-700">
                    {{ __('Your ghost kitchen account is awaiting admin approval.') }}
                </p>
                <p class="mt-2 text-sm text-gray-500">
                    {{ __("You'll be able to create meal plans once an admin approves your kitchen.") }}
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
