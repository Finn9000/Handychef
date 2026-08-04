<x-guest-layout>
    <div class="space-y-4">
        <p class="text-sm text-gray-600 text-center">{{ __('How would you like to sign up?') }}</p>

        <a href="{{ route('register.customer') }}"
            class="block w-full text-center px-4 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-500">
            {{ __('Sign up as Customer') }}
        </a>

        <a href="{{ route('register.kitchen') }}"
            class="block w-full text-center px-4 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700">
            {{ __('Sign up as Ghost Kitchen') }}
        </a>

        <div class="flex items-center justify-center mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
        </div>
    </div>
</x-guest-layout>
