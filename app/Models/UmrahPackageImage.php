<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UmrahPackageImage extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function package()
    {
        return $this->belongsTo(UmrahPackage::class, 'umrah_package_id');
    }
}
