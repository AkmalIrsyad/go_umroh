<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManasikGroup extends Model
{
    protected $guarded = [];

    //

    public function manasiks()
    {
        return $this->hasMany(Manasik::class);
    }
}
