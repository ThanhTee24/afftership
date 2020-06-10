<?php

namespace App\Http\Controllers\Api;

use App\Model\Carrier;
use App\Model\Tracking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class PaypalTrackingController extends Controller
{
    public function PaypalTracking(Request $request)
    {

        $carrier = Carrier::where('courier_tracking', '=', $request->carrier)->select('carrier_paypal')->get()->toArray();


        if (empty($carrier)) {


            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "http://api.paypal.esrax.com/trackers/?email=" . $request->paypal_account . "&transacionId=" . $request->transaction_id .
                    "&trackingNumber=" . $request->tracking . "&carrier=" . $request->carrier . "&status=SHIPPED",
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

            if(isset($json->status)) {
                Tracking::where('tracking_number', $request->tracking)->update(['update_status' => 'Updated']);
                //Log::channel('tracking_history')->info($response);
            }elseif(isset($json->errors)){
                $errors = $json->errors;
                foreach ($errors as $value){
                    Tracking::where('tracking_number', $request->tracking)->update(['update_status' => $errors->name]);
                    //Log::channel('tracking_history')->info($response);
                }
            }elseif(isset($json->name)){
                Tracking::where('tracking_number', $request->tracking)->update(['update_status' => $json->name]);
                //Log::channel('tracking_history')->info($response);
            }else{
                //Log::channel('tracking_history')->info($response);
            }

        } else {

            foreach ($carrier as $key => $value){

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "http://api.paypal.esrax.com/trackers/?email=" . $request->paypal_account . "&transacionId=" . $request->transaction_id .
                        "&trackingNumber=" . $request->tracking . "&carrier=" . $value['carrier_paypal'] . "&status=SHIPPED",
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

                if(isset($json->status)) {
                    Tracking::where('tracking_number', $request->tracking)->update(['update_status' => 'Updated']);
                    //Log::channel('tracking_history')->info($response);
                }elseif(isset($json->errors)){
                    $errors = $json->errors;
                    foreach ($errors as $value){
                        Tracking::where('tracking_number', $request->tracking)->update(['update_status' => $errors->name]);
                        //Log::channel('tracking_history')->info($response);
                    }
                }elseif(isset($json->name)){
                        Tracking::where('tracking_number', $request->tracking)->update(['update_status' => $json->name]);
                    //Log::channel('tracking_history')->info($response);
                }else{
                    //Log::channel('tracking_history')->info($response);
                }
            }

        }

        return back();

    }
}
