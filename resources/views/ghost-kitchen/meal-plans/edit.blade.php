<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Meal Plan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('kitchen.meal-plans.update', $mealPlan) }}" enctype="multipart/form-data">
                    @method('PUT')
                    @include('ghost-kitchen.meal-plans._form')

                    <div class="flex items-center justify-between mt-6">
                        <a href="{{ route('kitchen.meal-plans.items.index', $mealPlan) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                            {{ __('Manage Meal Items →') }}
                        </a>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('kitchen.meal-plans.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>{{ __('Save Changes') }}</x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
