<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    //

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(UmrahPackage::class, 'umrah_package_id');
    }

    public function participants()
    {
        return $this->hasMany(OrderParticipant::class);
    }

    public function paymentConfirmation()
    {
        return $this->hasOne(PaymentConfirmation::class);
    }
}
