<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    protected $table = 'tracking';

    protected $fillable = [
        'order_date', 'order_id', 'courier', 'tracking_number', 'supplier_access',
        'paypal_account', 'transaction_id', 'supplier', 'order_status', 'tracking_date'
    ];

    public function scopeOrder_date($query, $order_date){
        return $query->where('tracking.order_date', $order_date);
    }

    public function scopeOrder_id($query, $order_id){
        return $query->where('order_id', 'like', '%'. $order_id . '%');
    }

    public function scopeCourier($query, $courier){
        return $query->where('courier', 'like', '%'. $courier . '%');
    }

    public function scopeTracking_number($query, $tracking_number){
        return $query->where('tracking.tracking_number', 'like', '%'. $tracking_number . '%');
    }

    public function scoprTracking_date($query, $tracking_date){
        return $query->where('tracking.tracking_date', $tracking_date);
    }

    public function scopeNote($query, $note){
        return $query->where('tracking.note', 'like', '%'. $note . '%');
    }

    public function scopeCount_day($query, $count_day){
        return $query->where('tracking.count_day', 'like', '%'. $count_day . '%');
    }

    public function Detail(){
        return $this->belongsTo('App\Model\Detail','tracking_number','tracking_number');
    }
}
