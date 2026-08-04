<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Meal Items — ') }}{{ $mealPlan->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-green-100 text-green-800 rounded-md text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center justify-between">
                <span class="text-sm font-medium text-gray-700">{{ __('Plan price') }}</span>
                <span class="text-lg font-semibold text-gray-900">${{ number_format($mealPlan->price, 2) }}/week</span>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Add a Meal Item</h3>
                <form method="POST" action="{{ route('kitchen.meal-plans.items.store', $mealPlan) }}" class="space-y-4" enctype="multipart/form-data">
                    @csrf
                    <div>
                        <x-input-label for="name" value="Meal Name" />
                        <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name')" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="description" value="Description" />
                        <textarea id="description" name="description" rows="2"
                            class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <x-input-label for="image" value="Meal Image" />
                        <input id="image" name="image" type="file" accept="image/png,image/jpeg,image/webp" class="block mt-1 w-full text-sm text-gray-700" />
                        <p class="mt-1 text-xs text-gray-500">Optional. JPG, PNG, or WebP, up to 2 MB.</p>
                        <x-input-error :messages="$errors->get('image')" class="mt-2" />
                    </div>
                    <x-primary-button>{{ __('Add Item') }}</x-primary-button>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if ($mealItems->isEmpty())
                    <div class="p-6 text-gray-500 text-sm">{{ __('No meal items yet.') }}</div>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach ($mealItems as $item)
                            <li class="p-4">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $item->name }}</p>
                                        @if ($item->description)
                                            <p class="text-sm text-gray-500">{{ $item->description }}</p>
                                        @endif
                                        @if ($item->image_path)
                                            <img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->name }}" class="mt-2 h-20 w-28 rounded-md object-cover" />
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <form action="{{ route('kitchen.meal-plans.items.update', [$mealPlan, $item]) }}" method="POST" class="flex items-center gap-2" enctype="multipart/form-data">
                                            @csrf
                                            @method('PATCH')
                                            <input id="image-{{ $item->id }}" name="image" type="file" accept="image/png,image/jpeg,image/webp" class="max-w-40 text-xs" />
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md text-xs font-semibold text-white uppercase tracking-wide bg-indigo-600 hover:bg-indigo-700">Save</button>
                                        </form>
                                        <form action="{{ route('kitchen.meal-plans.items.destroy', [$mealPlan, $item]) }}" method="POST"
                                            onsubmit="return confirm('Remove this meal item?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md text-xs font-semibold text-white uppercase tracking-wide bg-red-600 hover:bg-red-700">Remove</button>
                                        </form>
                                    </div>
                                </div>

                                <div class="mt-3 ms-0 sm:ms-4 bg-gray-50 border border-gray-200 rounded-md p-3">
                                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ __('Ingredient Options') }}</h4>

                                    @if ($item->ingredientOptions->isEmpty())
                                        <p class="text-xs text-gray-400 mb-2">{{ __('No ingredient options yet.') }}</p>
                                    @else
                                        <ul class="space-y-1 mb-3">
                                            @foreach ($item->ingredientOptions as $option)
                                                <li class="flex items-center justify-between gap-2 text-sm">
                                                    <span>
                                                        <span class="px-1.5 py-0.5 text-xs rounded-full {{ $option->type === 'add' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                                                            {{ $option->type === 'add' ? 'Add' : 'Remove' }}
                                                        </span>
                                                        {{ $option->name }}
                                                        @if ($option->price_delta > 0)
                                                            <span class="text-gray-500">(+${{ number_format($option->price_delta, 2) }})</span>
                                                        @endif
                                                    </span>
                                                    <form action="{{ route('kitchen.meal-plans.items.ingredient-options.destroy', [$mealPlan, $item, $option]) }}" method="POST"
                                                        onsubmit="return confirm('Remove this ingredient option?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 text-xs">{{ __('Remove') }}</button>
                                                    </form>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    <form action="{{ route('kitchen.meal-plans.items.ingredient-options.store', [$mealPlan, $item]) }}" method="POST" class="flex flex-wrap items-end gap-2">
                                        @csrf
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">{{ __('Name') }}</label>
                                            <input type="text" name="name" placeholder="e.g. Extra Chicken" class="text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">{{ __('Type') }}</label>
                                            <select name="type" class="text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                <option value="add">{{ __('Add') }}</option>
                                                <option value="remove">{{ __('Remove') }}</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">{{ __('Price') }}</label>
                                            <input type="number" name="price_delta" step="0.01" min="0" value="0" class="w-20 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        </div>
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md text-xs font-semibold text-white uppercase tracking-wide bg-gray-800 hover:bg-gray-700">{{ __('Add Option') }}</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <a href="{{ route('kitchen.meal-plans.edit', $mealPlan) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                ← Back to Plan
            </a>
        </div>
    </div>
</x-app-layout>
