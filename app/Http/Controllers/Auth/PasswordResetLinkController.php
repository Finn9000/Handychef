<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Reset the user's password directly — no emailed link involved.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'reset_code' => ['required', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($request->reset_code !== config('app.password_reset_code')) {
            throw ValidationException::withMessages([
                'reset_code' => __('That reset code is incorrect.'),
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        return redirect()->route('login')->with('status', __('Your password has been reset.'));
    }
}
