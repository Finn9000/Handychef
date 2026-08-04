@csrf

<div>
    <x-input-label for="name" value="Plan Name" />
    <x-text-input id="name" name="name" type="text" class="block mt-1 w-full"
        :value="old('name', $mealPlan?->name ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="description" value="Description" />
    <textarea id="description" name="description" rows="3"
        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $mealPlan?->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="image" value="Meal Plan Image" />
    <input id="image" name="image" type="file" accept="image/png,image/jpeg,image/webp" class="block mt-1 w-full text-sm text-gray-700" />
    <p class="mt-1 text-xs text-gray-500">Optional. JPG, PNG, or WebP, up to 2 MB.</p>
    <x-input-error :messages="$errors->get('image')" class="mt-2" />
    @if ($mealPlan?->image_path)
        <img src="{{ asset('storage/'.$mealPlan->image_path) }}" alt="Current plan image" class="mt-3 h-28 w-44 rounded-md object-cover" />
    @endif
</div>

<div class="mt-4">
    <label class="flex items-center">
        <input type="checkbox" name="use_item_photos" value="1"
            class="rounded text-indigo-600 focus:ring-indigo-500"
            {{ old('use_item_photos', $mealPlan?->use_item_photos ?? false) ? 'checked' : '' }}>
        <span class="ms-2 text-sm text-gray-700">Use meal item photos as a collage instead of a single image</span>
    </label>
    <p class="mt-1 text-xs text-gray-500">If enabled, a grid of your meal items' own photos is shown instead of the image above once you've added items with photos.</p>
</div>

<div class="mt-4 grid grid-cols-2 gap-4">
    <div>
        <x-input-label value="Plan Price" />
        <p class="mt-1 text-sm text-gray-500">Calculated automatically from the prices of its meal items.</p>
    </div>

    <div>
        <x-input-label for="meals_per_week" value="Meals per Week" />
        <x-text-input id="meals_per_week" name="meals_per_week" type="number" min="1" max="21" class="block mt-1 w-full"
            :value="old('meals_per_week', $mealPlan?->meals_per_week ?? 5)" required />
        <x-input-error :messages="$errors->get('meals_per_week')" class="mt-2" />
    </div>
</div>

<div class="mt-4">
    <label class="flex items-center">
        <input type="checkbox" name="is_active" value="1"
            class="rounded text-indigo-600 focus:ring-indigo-500"
            {{ old('is_active', $mealPlan?->is_active ?? true) ? 'checked' : '' }}>
        <span class="ms-2 text-sm text-gray-700">Active (visible to customers)</span>
    </label>
</div>
