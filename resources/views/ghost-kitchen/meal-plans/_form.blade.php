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

<div class="mt-4">
    <x-input-label for="price" value="Plan Price (USD / week)" />
    <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="block mt-1 w-full"
        :value="old('price', $mealPlan?->price ?? '')" required />
    <x-input-error :messages="$errors->get('price')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label value="Available Days" />
    <p class="mt-1 text-xs text-gray-500">Which days of the week this plan runs, e.g. Monday–Thursday.</p>
    <div class="mt-2 grid grid-cols-2 sm:grid-cols-4 gap-2">
        @php $selectedDays = old('available_days', $mealPlan?->available_days ?? []); @endphp
        @foreach (\App\Models\MealPlan::DAYS as $key => $label)
            <label class="flex items-center">
                <input type="checkbox" name="available_days[]" value="{{ $key }}"
                    class="rounded text-indigo-600 focus:ring-indigo-500"
                    {{ in_array($key, $selectedDays) ? 'checked' : '' }}>
                <span class="ms-2 text-sm text-gray-700">{{ $label }}</span>
            </label>
        @endforeach
    </div>
    <x-input-error :messages="$errors->get('available_days')" class="mt-2" />
</div>

<div class="mt-4">
    <label class="flex items-center">
        <input type="checkbox" name="is_active" value="1"
            class="rounded text-indigo-600 focus:ring-indigo-500"
            {{ old('is_active', $mealPlan?->is_active ?? true) ? 'checked' : '' }}>
        <span class="ms-2 text-sm text-gray-700">Active (visible to customers)</span>
    </label>
</div>
