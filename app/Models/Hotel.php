<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $guarded = [];

    //

    public function packages()
    {
        return $this->belongsToMany(UmrahPackage::class, 'umrah_package_hotels');
    }
}
