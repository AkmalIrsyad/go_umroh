<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register a new user or merchant.
     *
     * @param  array{name: string, email: string, password: string, phone_number?: string, business_name?: string, business_information?: string, role?: string}  $data
     */
    public function register(array $data): User
    {
        $role = $data['role'] ?? 'user';

        /** @var User $user */
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone_number' => $data['phone_number'] ?? null,
            'business_name' => $data['business_name'] ?? null,
            'business_information' => $data['business_information'] ?? null,
            'status' => $role === 'merchants' ? 'pending' : 'approved',
        ]);

        $user->assignRole($role);

        return $user;
    }

    /**
     * Attempt to login and return a Sanctum token.
     *
     * @param  array{email: string, password: string}  $credentials
     *
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        /** @var User|null $user */
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user->load('roles'),
            'token' => $token,
        ];
    }

    /**
     * Revoke the current user's token.
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
