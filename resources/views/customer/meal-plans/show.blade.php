<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $mealPlan->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <x-meal-plan-image :plan="$mealPlan" class="mb-5 h-64 w-full" />
                <p class="text-sm text-gray-500">{{ $mealPlan->ghostKitchen->business_name }}</p>
                <p class="mt-2 text-gray-700">{{ $mealPlan->description }}</p>
                <div class="mt-4 flex items-center gap-3">
                    <span class="font-semibold text-lg text-gray-900">${{ number_format($mealPlan->price, 2) }}/week</span>
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">{{ $mealPlan->meals_per_week }} meals/week</span>
                </div>
            </div>

            @if ($hasActiveSubscription)
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg p-4 text-sm">
                    {{ __('You already have an active subscription.') }}
                    <a href="{{ route('customer.subscription.show') }}" class="underline font-medium">{{ __('View it here.') }}</a>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">{{ __('Choose your meals') }}</h3>

                    @if ($mealPlan->mealItems->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('This kitchen has not added meal items yet.') }}</p>
                    @else
                        <form method="POST" action="{{ route('customer.subscriptions.store', $mealPlan) }}" id="subscribe-form">
                            @csrf
                            <div class="space-y-2">
                                @foreach ($mealPlan->mealItems as $item)
                                    <label class="flex items-start gap-3 p-3 border border-gray-200 rounded-md">
                                        <input type="checkbox" name="meal_item_ids[]" value="{{ $item->id }}"
                                            data-price="{{ $item->price }}" class="item-checkbox mt-1 rounded text-indigo-600 focus:ring-indigo-500">
                                        @if ($item->image_path)
                                            <img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->name }}" class="h-16 w-20 rounded object-cover" />
                                        @endif
                                        <span class="flex-1">
                                            <span class="flex items-center justify-between gap-2">
                                                <span class="block text-sm font-medium text-gray-900">{{ $item->name }}</span>
                                                <span class="block text-sm font-medium text-gray-700">${{ number_format($item->price, 2) }}</span>
                                            </span>
                                            @if ($item->description)
                                                <span class="block text-sm text-gray-500">{{ $item->description }}</span>
                                            @endif
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('meal_item_ids')" class="mt-2" />

                            <div class="mt-4 flex items-center justify-between border-t border-gray-200 pt-4">
                                <span class="text-sm font-medium text-gray-700">{{ __('Selected total') }}</span>
                                <span id="selected-total" class="text-lg font-semibold text-gray-900">$0.00</span>
                            </div>

                            <div class="mt-4 border-t border-gray-200 pt-4">
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">{{ __('Pickup Details') }}</h4>
                                <x-input-label for="pickup_time" :value="__('Pickup Time')" />
                                <select id="pickup_time" name="pickup_time" required
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="" disabled selected>{{ __('Select a time slot') }}</option>
                                    @foreach (\App\Models\Subscription::PICKUP_TIME_SLOTS as $slot)
                                        <option value="{{ $slot }}" {{ old('pickup_time') === $slot ? 'selected' : '' }}>{{ $slot }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('pickup_time')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label :value="__('Pickup Location')" />
                                <p id="pickup-address-label" class="mt-1 text-sm text-gray-500">{{ __('Click the map to choose a pickup spot.') }}</p>
                                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
                                <div id="pickup-map" class="mt-2 rounded-md border border-gray-200" style="height: 300px"></div>
                                <input type="hidden" id="pickup_location" name="pickup_location" value="{{ old('pickup_location') }}">
                                <input type="hidden" id="pickup_latitude" name="pickup_latitude" value="{{ old('pickup_latitude') }}">
                                <input type="hidden" id="pickup_longitude" name="pickup_longitude" value="{{ old('pickup_longitude') }}">
                                <x-input-error :messages="$errors->get('pickup_location')" class="mt-2" />
                                <x-input-error :messages="$errors->get('pickup_latitude')" class="mt-2" />
                            </div>

                            <div class="mt-4 border-t border-gray-200 pt-4">
                                <button type="button" id="toggle-customization"
                                    class="text-sm text-indigo-600 hover:text-indigo-900 underline">
                                    {{ __('+ Add ingredient request') }}
                                </button>
                                <div id="customization-box" class="hidden mt-2">
                                    <x-input-label for="customization_notes" :value="__('Ingredient requests (e.g. remove onions, no peanuts)')" />
                                    <textarea id="customization_notes" name="customization_notes" rows="3"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('customization_notes') }}</textarea>
                                    <x-input-error :messages="$errors->get('customization_notes')" class="mt-2" />
                                </div>
                            </div>

                            <x-primary-button class="mt-4">{{ __('Subscribe') }}</x-primary-button>
                        </form>

                        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
                        <script>
                            (function () {
                                const map = L.map('pickup-map').setView([11.5564, 104.9282], 13);

                                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                    attribution: '&copy; OpenStreetMap contributors',
                                    maxZoom: 19,
                                }).addTo(map);

                                let marker = null;
                                const addressLabel = document.getElementById('pickup-address-label');
                                const locationInput = document.getElementById('pickup_location');
                                const latInput = document.getElementById('pickup_latitude');
                                const lngInput = document.getElementById('pickup_longitude');

                                map.on('click', function (e) {
                                    const { lat, lng } = e.latlng;

                                    if (marker) {
                                        marker.setLatLng(e.latlng);
                                    } else {
                                        marker = L.marker(e.latlng).addTo(map);
                                    }

                                    latInput.value = lat;
                                    lngInput.value = lng;
                                    addressLabel.textContent = 'Looking up address…';

                                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                                        .then((response) => response.json())
                                        .then((data) => {
                                            const label = data.display_name || `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
                                            locationInput.value = label;
                                            addressLabel.textContent = label;
                                        })
                                        .catch(() => {
                                            const fallback = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
                                            locationInput.value = fallback;
                                            addressLabel.textContent = fallback;
                                        });
                                });
                            })();

                            (function () {
                                const checkboxes = document.querySelectorAll('#subscribe-form .item-checkbox');
                                const totalEl = document.getElementById('selected-total');

                                function updateTotal() {
                                    let total = 0;
                                    checkboxes.forEach((cb) => {
                                        if (cb.checked) {
                                            total += parseFloat(cb.dataset.price) || 0;
                                        }
                                    });
                                    totalEl.textContent = '$' + total.toFixed(2);
                                }

                                checkboxes.forEach((cb) => cb.addEventListener('change', updateTotal));
                                updateTotal();
                            })();

                            (function () {
                                const toggleBtn = document.getElementById('toggle-customization');
                                const box = document.getElementById('customization-box');

                                toggleBtn.addEventListener('click', () => {
                                    box.classList.toggle('hidden');
                                });
                            })();
                        </script>
                    @endif
                </div>
            @endif

            <a href="{{ route('customer.meal-plans.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                ← {{ __('Back to plans') }}
            </a>
        </div>
    </div>
</x-app-layout>
