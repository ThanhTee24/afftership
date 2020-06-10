<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Carrier;
use App\Model\Tracking;
use Illuminate\Support\Facades\Log;

class AddPaypalController extends Controller
{
    public function add_paypal(Request $request)
    {


        $carrier = Carrier::where('courier_tracking', '=', $request->carrier)->select('carrier_paypal')->get()->toArray();

        if (empty($carrier) == true) {
            $courier = str_replace(" ","%20",$request->carrier);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "http://api.paypal.esrax.com/trackers/?email=".$request->paypal_account."&transacionId=".$request->transaction_id."&trackingNumber=".$request->tracking."&carrier=OTHER&carrierName=".$courier."&status=SHIPPED&type=add",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "X-Auth-Client: {{client_id}}",
                    "X-Auth-Token: {{client_secret}}",
                    "Accept: application/json",
                    "Content-Type: application/json"
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $json = json_decode($response);

            if (isset($json->tracker_identifiers)) {
                $tracker_identifiers = $json->tracker_identifiers;
                if ($tracker_identifiers != null) {
                    Tracking::where('tracking_number', $request->tracking)->update(['update_status' => 'Added']);
//                Log::channel('tracking_history')->info($response);
                } else {
                    Tracking::where('tracking_number', $request->tracking)->update(['update_status' => 'Error']);
//                Log::channel('tracking_history')->info($response);
                }
            } else {
                Tracking::where('tracking_number', $request->tracking)->update(['update_status' => 'Error']);
//            Log::channel('tracking_history')->info($response);
            }

        } else {

            foreach ($carrier as $key => $value) {
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "http://api.paypal.esrax.com/trackers/?email=" . $request->paypal_account . "&transacionId=" . $request->transaction_id . "&trackingNumber=" . $request->tracking . "&carrier=" . $value['carrier_paypal'] . "&status=SHIPPED&type=add",
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

                if (isset($json->tracker_identifiers)) {
                    $tracker_identifiers = $json->tracker_identifiers;
                    if ($tracker_identifiers != null) {
                        Tracking::where('tracking_number', $request->tracking)->update(['update_status' => 'Added']);
//                Log::channel('tracking_history')->info($response);
                    } else {
                        Tracking::where('tracking_number', $request->tracking)->update(['update_status' => 'Error']);
//                Log::channel('tracking_history')->info($response);
                    }
                } else {
                    Tracking::where('tracking_number', $request->tracking)->update(['update_status' => 'Error']);
//            Log::channel('tracking_history')->info($response);
                }
            }
        }
        return back();

    }

}
