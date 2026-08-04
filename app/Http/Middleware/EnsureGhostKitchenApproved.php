<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGhostKitchenApproved
{
    /**
     * Redirect ghost kitchen users to the pending page until an admin approves them.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $kitchen = $request->user()->ghostKitchen;

        if (! $kitchen || $kitchen->status !== 'approved') {
            return redirect()->route('kitchen.pending');
        }

        return $next($request);
    }
}
