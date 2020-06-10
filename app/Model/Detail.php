<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    protected $table = 'detail';

    protected $fillable = [
        'tracking_number', 'process_content', 'process_date',
        'tracking_status', 'step_number', 'delivery_status', 'total_day', 'note', 'tracking_id'
    ];

    public function scopeProcess_content($query, $process_content){
        return $query->where('process_content', 'like', '%'. $process_content . '%');
    }

    public function scoprProcess_date($query, $process_date){
        return $query->where('detail.process_date', $process_date);
    }

    public function scoprTotal($query, $total){
        return $query->where('detail.total_day', $total);
    }

    public function Detail(){
        return $this->hasOne('App\Model\Delivery_status','id','id');
    }

    public function Tracking(){
        return $this->hasOne('App\Model\Tracking','tracking_number','tracking_number');
    }
}
