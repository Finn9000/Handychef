<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GhostKitchen;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GhostKitchenController extends Controller
{
    public function index(): View
    {
        $kitchens = GhostKitchen::with('user')
            ->withCount('mealPlans')
            ->latest()
            ->get();

        return view('admin.kitchens.index', compact('kitchens'));
    }

    public function approve(GhostKitchen $kitchen): RedirectResponse
    {
        $kitchen->update(['status' => 'approved']);

        return redirect()->route('admin.kitchens.index')->with('status', 'Ghost kitchen approved.');
    }

    public function destroy(GhostKitchen $kitchen): RedirectResponse
    {
        // Deleting the underlying user cascades to the kitchen (foreign key cascadeOnDelete).
        $kitchen->user()->delete();

        return redirect()->route('admin.kitchens.index')->with('status', 'Ghost kitchen removed.');
    }
}
