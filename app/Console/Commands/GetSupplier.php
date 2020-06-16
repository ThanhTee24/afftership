<?php

namespace App\Console\Commands;

use App\Model\Tracking;
use Illuminate\Console\Command;

class GetSupplier extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Get:Supplier';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get courier, tracking number to Supplier';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function call_novel($order_id)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://plus.esrax.com/Api/novel3d/?OrderId=AN" . $order_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $json = json_decode($response);

        return $json;
    }

    public function mapping_inovel($tracking_number)
    {
        if (preg_match('/YT/', $tracking_number) == true) {
            $courier = 'Yun Express';
        } elseif ($tracking_number == null) {
            $courier = null;
        } else {
            $courier = 'USPS';
        }

        return $courier;
    }

    public function handing_inovel($json, $order_id)
    {
        if ($json->code == '200') {

            $result = $json->result;

            $courier = $this->mapping_inovel($result->waybillCode);

            $order_id_update = array(
                'order_id' => $order_id
            );

            $form_data = array(
                'tracking_number' => $result->waybillCode,
                'courier' => $courier,
                'supplier_access' => 1
            );

            var_dump($form_data);

            Tracking::updateOrCreate($order_id_update, $form_data);

        }
    }

    public function call_ibedding($order_id){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://plus.esrax.com/Api/ibedding/?OrderId=" . $order_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $json = json_decode($response);

        return $json;
    }

    public function handling_ibedding($json, $order_id){
        if (isset($json->orderDataList[0])) {
            $orderDataList = $json->orderDataList[0];

            $order_id_update = array(
                'order_id' => $order_id
            );

            $form_data = array(
                'tracking_number' => $orderDataList->trackNumber,
                'courier' => $orderDataList->shippingService,
                'supplier_access' => 1
            );
            var_dump($form_data);

            Tracking::updateOrCreate($order_id_update, $form_data);
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
//        Inovel call supplier Leon
//        $inovel = Tracking::select('order_id')->where('supplier', 'Leon')
//            ->where('tracking_number', '=', null)->get();
//        foreach ($inovel as $value) {
//
//            $order_id = $value->order_id;
//            var_dump($order_id);
//            $json = $this->call_novel($order_id);
//
//            $form_data = $this->handing_inovel($json, $order_id);
//
//        }

//        Ibedding call supplier Tony
        $ibeđing = Tracking::select('order_id')->where('supplier', 'Tony')
            ->where('tracking_number', null)->get();

        foreach ($ibeđing as $value) {

            $order_id = $value->order_id;
            var_dump($order_id);

            $json = $this->call_ibedding($order_id);

            $form_data = $this->handling_ibedding($json, $order_id);
        }
    }
}
