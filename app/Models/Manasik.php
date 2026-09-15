<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manasik extends Model
{
    protected $guarded = [];

    public function group()
    {
        return $this->belongsTo(ManasikGroup::class, 'manasik_group_id');
    }
}
