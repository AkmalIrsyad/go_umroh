<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());

        return response()->json([
            'message' => 'Registrasi berhasil.',
            'data' => new UserResource($user),
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return response()->json([
            'message' => 'Login berhasil.',
            'data' => [
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => new UserResource($request->user()->load('roles')),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json(['message' => 'Logout berhasil.']);
    }

    /**
     * Redirect to Google OAuth provider.
     */
    public function redirectToGoogle(Request $request): JsonResponse|RedirectResponse
    {
        $role = $request->query('role', 'user');
        $state = base64_encode(json_encode(['role' => $role]));

        /** @var \Laravel\Socialite\Two\GoogleProvider $driver */
        $driver = Socialite::driver('google')->stateless();
        $targetUrl = $driver->with(['state' => $state])->redirect()->getTargetUrl();

        if ($request->wantsJson()) {
            return response()->json([
                'url' => $targetUrl,
            ]);
        }

        return redirect()->away($targetUrl);
    }

    /**
     * Handle callback from Google OAuth provider.
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            $role = 'user';
            if ($request->filled('state')) {
                $decoded = json_decode(base64_decode((string) $request->query('state')), true);
                if (is_array($decoded) && isset($decoded['role'])) {
                    $role = $decoded['role'];
                }
            }

            /** @var \Laravel\Socialite\Two\GoogleProvider $driver */
            $driver = Socialite::driver('google')->stateless();
            $googleUser = $driver->user();

            $result = $this->authService->handleGoogleUser($googleUser, $role);

            $query = http_build_query([
                'token' => $result['token'],
            ]);

            return redirect('/auth/google/callback?' . $query);
        } catch (Throwable $e) {
            $message = $e->getMessage() ?: 'Autentikasi dengan Google dibatalkan atau gagal.';
            return redirect('/login?error=' . urlencode($message));
        }
    }
}
