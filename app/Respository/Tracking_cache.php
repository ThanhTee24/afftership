<?php


namespace App\Respository;

use App\Model\Detail;
use App\Model\Tracking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Tracking_cache{

    const CACHE_KEY = 'TRACKING';

    public function all($orderBy){

        $key = "all.{$orderBy}";

        $cacheKey = $this->getCacheKey($key);

        return cache()->remember($cacheKey, Carbon::now()->addMinute(1), function () use ($orderBy){
            return Tracking::latest()
                ->leftJoin('detail', function ($join) {
                    $join->on('tracking.tracking_number', '=', 'detail.tracking_number')
                        ->where('detail.step_number', '=', 0);
                })
                ->leftJoin('delivery_status', 'detail.delivery_status', '=', 'delivery_status.id')

                ->select( 'tracking.id', 'tracking.order_date', 'tracking.paypal_account', 'tracking.transaction_id', 'tracking.order_id', 'tracking.tracking_number', 'tracking.courier', 'tracking.tracking_date', 'tracking.supplier'
                    ,'tracking.approved','tracking.count_day', 'tracking.note', 'tracking.created_at','tracking.count_day', 'detail.process_content', 'detail.process_date', 'delivery_status.name as status',
                    'detail.total_day as total')
                ->orderBy($orderBy)
                ->get();
        });


    }

    public function get($id){

    }

    public function json(){
//        return DB::select("SELECT t1.* FROM tracking t1 JOIN detail t2 ON t1.tracking_number = t2.tracking_number JOIN delivery_status t3 ON t2.delivery_status = t3.id WHERE t2.step_number = 0");

        return DB::table('tracking')->latest()
            ->leftJoin('detail', function ($join) {
                $join->on('tracking.tracking_number', '=', 'detail.tracking_number')
                    ->where('detail.step_number', '=', 0);
            })
            ->leftJoin('delivery_status', 'detail.delivery_status', '=', 'delivery_status.id')
            ->select('tracking.*', 'detail.process_content', 'detail.process_date', 'delivery_status.name as status',
                'detail.total_day as total')
            ->get();
    }

    public function getCacheKey($key)
    {
        $key = strtoupper($key);

        return self::CACHE_KEY .".$key";
    }
}
