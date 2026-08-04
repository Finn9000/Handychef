<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = User::where('role', User::ROLE_CUSTOMER)
            ->withCount('subscriptions')
            ->latest()
            ->get();

        return view('admin.customers.index', compact('customers'));
    }

    public function destroy(User $customer): RedirectResponse
    {
        if ($customer->role !== User::ROLE_CUSTOMER) {
            abort(404);
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')->with('status', 'Customer removed.');
    }
}
