<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqGroup extends Model
{
    protected $guarded = [];

    //

    public function faqs()
    {
        return $this->hasMany(Faq::class);
    }
}
