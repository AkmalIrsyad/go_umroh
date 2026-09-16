<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UmrahPackage extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'price' => 'decimal:2',
            'booking_fee' => 'decimal:2',
            'capacity' => 'integer',
            'booked_count' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $package): void {
            if (empty($package->slug)) {
                $package->slug = self::generateUniqueSlug($package->title);
            }
        });

        static::updating(function (self $package): void {
            if ($package->isDirty('title') && empty($package->slug)) {
                $package->slug = self::generateUniqueSlug($package->title, $package->id);
            }
        });
    }

    /**
     * Generate a URL-friendly slug from a title, ensuring uniqueness.
     */
    private static function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (true) {
            $query = self::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            if (! $query->exists()) {
                break;
            }

            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    public function airLine()
    {
        return $this->belongsTo(AirLine::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hotels()
    {
        return $this->belongsToMany(Hotel::class, 'umrah_package_hotels');
    }

    public function images()
    {
        return $this->hasMany(UmrahPackageImage::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
