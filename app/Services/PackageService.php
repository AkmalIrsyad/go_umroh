<?php

namespace App\Services;

use App\Models\UmrahPackage;
use App\Models\UmrahPackageImage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PackageService
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function getPublicPackages(array $filters): LengthAwarePaginator
    {
        $query = UmrahPackage::with(['airLine', 'route', 'hotels', 'images' => fn ($q) => $q->where('is_primary', true)])
            ->where('status', 'approved');

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (! empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (! empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }
        if (! empty($filters['airline_id'])) {
            $query->where('air_line_id', $filters['airline_id']);
        }
        if (! empty($filters['departure_date'])) {
            $query->whereDate('departure_date', '>=', $filters['departure_date']);
        }
        if (! empty($filters['search'])) {
            $query->where('title', 'like', '%'.$filters['search'].'%');
        }

        return $query->orderBy('departure_date')->paginate(12);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function getAdminPackages(array $filters): LengthAwarePaginator
    {
        $query = UmrahPackage::with(['airLine', 'route', 'user']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['search'])) {
            $query->where('title', 'like', '%'.$filters['search'].'%');
        }

        return $query->latest()->paginate(15);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function getMerchantPackages(int $merchantId, array $filters): LengthAwarePaginator
    {
        $query = UmrahPackage::with(['airLine', 'route'])->where('user_id', $merchantId);

        if (! empty($filters['search'])) {
            $query->where('title', 'like', '%'.$filters['search'].'%');
        }

        return $query->latest()->paginate(15);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createPackage(array $data, int $merchantId): UmrahPackage
    {
        $data['user_id'] = $merchantId;
        $data['status'] = 'pending';
        $hotelIds = $data['hotel_ids'] ?? [];
        unset($data['hotel_ids'], $data['images']);

        /** @var UmrahPackage $package */
        $package = UmrahPackage::create($data);
        $package->hotels()->sync($hotelIds);

        return $package->load(['airLine', 'route', 'hotels']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updatePackage(UmrahPackage $package, array $data): UmrahPackage
    {
        $hotelIds = $data['hotel_ids'] ?? null;
        unset($data['hotel_ids']);

        $package->update($data);
        if ($hotelIds !== null) {
            $package->hotels()->sync($hotelIds);
        }

        return $package->fresh(['airLine', 'route', 'hotels', 'images']);
    }

    public function storeImage(UmrahPackage $package, mixed $file, bool $isPrimary): UmrahPackageImage
    {
        $path = $file->store('packages', 'public');

        if ($isPrimary) {
            $package->images()->where('is_primary', true)->update(['is_primary' => false]);
        }

        return $package->images()->create(['path' => $path, 'is_primary' => $isPrimary]);
    }
}
