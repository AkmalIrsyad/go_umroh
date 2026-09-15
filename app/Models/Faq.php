<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $guarded = [];

    public function group()
    {
        return $this->belongsTo(FaqGroup::class, 'faq_group_id');
    }
}
