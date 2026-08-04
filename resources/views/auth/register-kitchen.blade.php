<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 bg-yellow-50 border border-yellow-200 rounded-md p-3">
        {{ __('New ghost kitchen accounts must be approved by an admin before you can create meal plans.') }}
    </div>

    <form method="POST" action="{{ route('register.kitchen') }}">
        @csrf

        <!-- Kitchen name -->
        <div>
            <x-input-label for="business_name" :value="__('Ghost Kitchen Name')" />
            <x-text-input id="business_name" class="block mt-1 w-full" type="text" name="business_name" :value="old('business_name')" required autofocus />
            <x-input-error :messages="$errors->get('business_name')" class="mt-2" />
        </div>

        <!-- Manager's name -->
        <div class="mt-4">
            <x-input-label for="manager_name" :value="__('Manager\'s Name')" />
            <x-text-input id="manager_name" class="block mt-1 w-full" type="text" name="manager_name" :value="old('manager_name')" required />
            <x-input-error :messages="$errors->get('manager_name')" class="mt-2" />
        </div>

        <!-- Location -->
        <div class="mt-4">
            <x-input-label for="address" :value="__('Location')" />
            <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" required />
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
        </div>

        <!-- Owner's phone number -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Owner\'s Phone Number')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('register') }}">
                {{ __('← Back') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
