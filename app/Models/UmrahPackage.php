<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
