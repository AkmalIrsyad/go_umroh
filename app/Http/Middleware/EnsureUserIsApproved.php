<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApproved
{
    /**
     * Handle an incoming request.
     * Block suspended (status = 'pending') users from accessing protected API endpoints.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->status === 'pending') {
            return response()->json([
                'message' => 'Akun Anda sedang ditangguhkan. Hubungi administrator untuk informasi lebih lanjut.',
                'error'   => 'account_suspended',
            ], 403);
        }

        return $next($request);
    }
}

