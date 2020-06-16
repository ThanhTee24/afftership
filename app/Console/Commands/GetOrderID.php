<?php

namespace App\Console\Commands;

use App\Model\Crawl_bighub;
use App\Model\Tracking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetOrderID extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Get:OrderID';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Order ID - Bighub';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $last_id = Crawl_bighub::select('id_last_order_id')
            ->orderBy('id', 'DESC')
            ->take(1)
            ->get();

        foreach ($last_id as $value) {
            $last = $value->id_last_order_id;
        }
        var_dump($last);
        //Kéo dữ liệu từ bighub database về.
        $results = DB::connection('bighub')->table('purchare_orders')
            ->join('suppliers', 'purchare_orders.supplier_id', '=', 'suppliers.id')
            ->join('orders', 'purchare_orders.order_id', '=', 'orders.id')
            ->join('purchase_order_statuses', 'purchare_orders.purchase_order_status_id', '=', 'purchase_order_statuses.id')
            ->select('purchare_orders.id', 'orders.order_id', 'orders.payment_provider_id', 'orders.date_created', 'suppliers.name as supplier', 'purchase_order_statuses.name as status')
            ->where('purchare_orders.id', '>', $last)
            ->orderBy('purchare_orders.id', 'ASC')
            ->get();

        if (count($results) > 0) {
            //Xử lý lại dữ liệu
            foreach ($results as $value) {
                $last_id = $value->id;
                $time = strtotime($value->date_created);

                $order_date = date('yy/m/d', $time);

                $order_id = array(
                    'order_id' => $value->order_id
                );

                $form_data = array(
                    'transaction_id' => $value->payment_provider_id,
                    'order_date' => $order_date,
                    'supplier' => $value->supplier,
                    'order_status' => $value->status
                );

                Tracking::updateOrCreate($order_id, $form_data);
                var_dump($order_id);

            }
            $last_data = array(
                'id_last_order_id' => $last_id
            );
            Crawl_bighub::create($last_data);
            var_dump("Saved last ID");
        } else {
            var_dump("Không có dữ liệu mới");
        }

    }
}
