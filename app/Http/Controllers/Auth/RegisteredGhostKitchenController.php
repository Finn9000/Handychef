<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredGhostKitchenController extends Controller
{
    public function create(): View
    {
        return view('auth.register-kitchen');
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'manager_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->business_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_GHOST_KITCHEN,
        ]);

        // Ghost kitchens skip email verification — their gate is admin approval instead.
        $user->forceFill(['email_verified_at' => now()])->save();

        $user->ghostKitchen()->create([
            'business_name' => $request->business_name,
            'manager_name' => $request->manager_name,
            'address' => $request->address,
            'phone' => $request->phone,
            'status' => 'pending',
        ]);

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
