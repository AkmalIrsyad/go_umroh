<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Package\StorePackageRequest;
use App\Http\Requests\Package\UpdatePackageRequest;
use App\Http\Resources\PackageResource;
use App\Models\UmrahPackage;
use App\Models\UmrahPackageImage;
use App\Services\PackageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PackageController extends Controller
{
    public function __construct(private readonly PackageService $packageService) {}

    public function index(Request $request): JsonResponse
    {
        $packages = $this->packageService->getPublicPackages($request->all());

        return response()->json(PackageResource::collection($packages)->response()->getData(true));
    }

    public function show(Request $request, UmrahPackage $package): JsonResponse
    {
        $user = $request->user('sanctum');
        $isAuthorized = $user && ($user->hasRole('admin') || $package->user_id === $user->id);

        if (! $isAuthorized && $package->status !== 'approved') {
            abort(404, 'Paket tidak ditemukan.');
        }

        return response()->json([
            'data' => new PackageResource($package->load(['airLine', 'route', 'hotels', 'images', 'user'])),
        ]);
    }

    public function adminIndex(Request $request): JsonResponse
    {
        $packages = $this->packageService->getAdminPackages($request->all());

        return response()->json(PackageResource::collection($packages)->response()->getData(true));
    }

    public function merchantIndex(Request $request): JsonResponse
    {
        $packages = $this->packageService->getMerchantPackages($request->user()->id, $request->all());

        return response()->json(PackageResource::collection($packages)->response()->getData(true));
    }

    public function store(StorePackageRequest $request): JsonResponse
    {
        $package = $this->packageService->createPackage($request->validated(), $request->user()->id);

        return response()->json(['data' => new PackageResource($package->load(['airLine', 'route', 'hotels', 'images', 'user']))], 201);
    }

    public function update(UpdatePackageRequest $request, UmrahPackage $package): JsonResponse
    {
        $user = $request->user();
        if (! $user->hasRole('admin') && $package->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah paket ini.');
        }

        $updated = $this->packageService->updatePackage($package, $request->validated());

        return response()->json(['data' => new PackageResource($updated->load(['airLine', 'route', 'hotels', 'images', 'user']))]);
    }

    public function updateStatus(Request $request, UmrahPackage $package): JsonResponse
    {
        $request->validate(['status' => ['required', 'in:pending,approved']]);
        $package->update(['status' => $request->status]);

        return response()->json(['message' => 'Status paket diperbarui.', 'data' => new PackageResource($package->load(['airLine', 'route', 'hotels', 'images', 'user']))]);
    }

    public function destroy(Request $request, UmrahPackage $package): JsonResponse
    {
        $user = $request->user();
        if (! $user->hasRole('admin') && $package->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $package->delete();

        return response()->json(['message' => 'Paket berhasil dihapus.']);
    }

    public function uploadImage(Request $request, UmrahPackage $package): JsonResponse
    {
        $user = $request->user();
        if (! $user->hasRole('admin') && $package->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'image' => ['required', 'image', 'max:5120'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        $isPrimary = $request->boolean('is_primary') || $package->images()->count() === 0;
        $image = $this->packageService->storeImage($package, $request->file('image'), $isPrimary);

        return response()->json([
            'message' => 'Foto berhasil diunggah.',
            'data' => [
                'id' => $image->id,
                'url' => asset('storage/'.$image->path),
                'is_primary' => $image->is_primary,
            ],
        ], 201);
    }

    public function deleteImage(Request $request, UmrahPackageImage $image): JsonResponse
    {
        $package = $image->package;
        $user = $request->user();
        if (! $user->hasRole('admin') && $package->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        Storage::disk('public')->delete($image->path);
        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary) {
            $next = $package->images()->first();
            if ($next) {
                $next->update(['is_primary' => true]);
            }
        }

        return response()->json(['message' => 'Foto berhasil dihapus.']);
    }

    public function setPrimaryImage(Request $request, UmrahPackageImage $image): JsonResponse
    {
        $package = $image->package;
        $user = $request->user();
        if (! $user->hasRole('admin') && $package->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $package->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return response()->json(['message' => 'Foto utama berhasil diatur.']);
    }
}
