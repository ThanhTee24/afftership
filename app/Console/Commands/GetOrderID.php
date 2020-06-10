<?php

namespace App\Console\Commands;

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
        //Kéo dữ liệu từ bighub database về.
        $results = DB::connection('bighub')->table('purchare_orders')
            ->join('suppliers', 'purchare_orders.supplier_id', '=', 'suppliers.id')
            ->join('orders', 'purchare_orders.order_id', '=', 'orders.id')
            ->join('purchase_order_statuses', 'purchare_orders.purchase_order_status_id', '=', 'purchase_order_statuses.id')
            ->select('orders.order_id', 'orders.payment_provider_id', 'orders.date_created', 'suppliers.name as supplier', 'purchase_order_statuses.name as status')
            ->get();

        //Xử lý lại dữ liệu
        foreach ($results as $value) {

            $time = strtotime($value->date_created);

            $order_date = date('yy/m/d', $time);

            $order_id = array(
                'order_id' => $value->order_id
            );

            $form_data = array(
                'transaction_id' => $value->payment_provider_id,
                'order_date' => $order_date,
                'supplier' => $value->supplier,
                'order_status'=> $value->status
            );

            Tracking::updateOrCreate($order_id, $form_data);
            var_dump($order_id);

        }

    }
}
