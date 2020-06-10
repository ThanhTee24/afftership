<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Delivery_status extends Model
{
    protected $table = 'delivery_status';

    protected $fillable = [
        'name'
    ];

    public function scopeStatus($query, $status){
        return $query->where('delivery_status.name', 'like', '%'. $status . '%');
    }
}
