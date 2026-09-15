<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::with('roles');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('email', 'like', '%'.$request->search.'%');
        }
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        return response()->json(UserResource::collection($query->latest()->paginate(15))->response()->getData(true));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:admin,merchants,user'],
            'status' => ['required', 'in:pending,approved'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'business_information' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone_number' => $data['phone_number'] ?? null,
            'status' => $data['status'] ?? 'approved',
            'business_name' => $data['business_name'] ?? null,
            'business_information' => $data['business_information'] ?? null,
        ]);

        $user->assignRole($data['role']);

        return response()->json([
            'message' => 'Pengguna berhasil dibuat.',
            'data' => new UserResource($user->load('roles')),
        ], 201);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json([
            'data' => new UserResource($user->load('roles')),
        ]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:6'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'role' => ['sometimes', 'in:admin,merchants,user'],
            'status' => ['sometimes', 'in:pending,approved'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'business_information' => ['nullable', 'string'],
        ]);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if (isset($data['role'])) {
            $user->syncRoles([$data['role']]);
            unset($data['role']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Pengguna berhasil diperbarui.',
            'data' => new UserResource($user->fresh('roles')),
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'phone_number' => ['sometimes', 'nullable', 'string'],
            'address' => ['sometimes', 'nullable', 'string'],
            'business_name' => ['sometimes', 'nullable', 'string'],
            'business_information' => ['sometimes', 'nullable', 'string'],
        ]);

        $request->user()->update($data);

        return response()->json(['data' => new UserResource($request->user()->fresh())]);
    }

    public function updateStatus(Request $request, User $user): JsonResponse
    {
        $request->validate(['status' => ['required', 'in:pending,approved']]);
        $user->update(['status' => $request->status]);

        return response()->json(['message' => 'Status pengguna diperbarui.', 'data' => new UserResource($user)]);
    }

    public function updateRole(Request $request, User $user): JsonResponse
    {
        $request->validate(['role' => ['required', 'in:admin,merchants,user']]);
        $user->syncRoles([$request->role]);

        return response()->json(['message' => 'Role pengguna diperbarui.', 'data' => new UserResource($user->load('roles'))]);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(['message' => 'Pengguna dihapus.']);
    }
}
