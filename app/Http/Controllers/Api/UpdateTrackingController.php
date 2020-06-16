<?php

namespace App\Http\Controllers\Api;

use App\Model\Detail;
use App\Model\Tracking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class UpdateTrackingController extends Controller
{

    public function find_date($string)
    {
        $shortenize = function ($string) {
            return substr($string, 0, 3);
        };

        // Define month name:
        $month_names = array(
            "january",
            "february",
            "march",
            "april",
            "may",
            "june",
            "july",
            "august",
            "september",
            "october",
            "november",
            "december"
        );
        $short_month_names = array_map($shortenize, $month_names);

        // Define day name
        $day_names = array(
            "monday",
            "tuesday",
            "wednesday",
            "thursday",
            "friday",
            "saturday",
            "sunday"
        );
        $short_day_names = array_map($shortenize, $day_names);

        // Define ordinal number
        $ordinal_number = ['st', 'nd', 'rd', 'th'];

        $day = "";
        $month = "";
        $year = "";

        // Match dates: 01/01/2012 or 30-12-11 or 1 2 1985
        preg_match('/([0-9]?[0-9])[\.\-\/ ]+([0-3]?[0-9])[\.\-\/ ]+([0-9]{2,4})/', $string, $matches);
        if ($matches) {
            if ($matches[1])
                $month = $matches[1];
            if ($matches[2])
                $day = $matches[2];
            if ($matches[3])
                $year = $matches[3];
        }

        // Match dates: Sunday 1st March 2015; Sunday, 1 March 2015; Sun 1 Mar 2015; Sun-1-March-2015
        preg_match('/(?:(?:' . implode('|', $day_names) . '|' . implode('|', $short_day_names) . ')[ ,\-_\/]*)?([0-9]?[0-9])[ ,\-_\/]*(?:' . implode('|', $ordinal_number) . ')?[ ,\-_\/]*(' . implode('|', $month_names) . '|' . implode('|', $short_month_names) . ')[ ,\-_\/]+([0-9]{4})/i', $string, $matches);
        if ($matches) {
            if (empty($day) && $matches[1])
                $day = $matches[1];

            if (empty($month) && $matches[2]) {
                $month = array_search(strtolower($matches[2]), $short_month_names);

                if (!$month)
                    $month = array_search(strtolower($matches[2]), $month_names);

                $month = $month + 1;
            }

            if (empty($year) && $matches[3])
                $year = $matches[3];
        }

        // Match dates: March 1st 2015; March 1 2015; March-1st-2015
        preg_match('/(' . implode('|', $month_names) . '|' . implode('|', $short_month_names) . ')[ ,\-_\/]*([0-9]?[0-9])[ ,\-_\/]*(?:' . implode('|', $ordinal_number) . ')?[ ,\-_\/]+([0-9]{4})/i', $string, $matches);
        if ($matches) {
            if (empty($month) && $matches[1]) {
                $month = array_search(strtolower($matches[1]), $short_month_names);

                if (!$month)
                    $month = array_search(strtolower($matches[1]), $month_names);

                $month = $month + 1;
            }

            if (empty($day) && $matches[2])
                $day = $matches[2];

            if (empty($year) && $matches[3])
                $year = $matches[3];
        }

        // Match month name:
        if (empty($month)) {
            preg_match('/(' . implode('|', $month_names) . ')/i', $string, $matches_month_word);
            if ($matches_month_word && $matches_month_word[1])
                $month = array_search(strtolower($matches_month_word[1]), $month_names);

            // Match short month names
            if (empty($month)) {
                preg_match('/(' . implode('|', $short_month_names) . ')/i', $string, $matches_month_word);
                if ($matches_month_word && $matches_month_word[1])
                    $month = array_search(strtolower($matches_month_word[1]), $short_month_names);
            }

            $temp = getdate();
            $month = $temp['mon'];
        }

        // Match 5th 1st day:
        if (empty($day)) {
            preg_match('/([0-9]?[0-9])(' . implode('|', $ordinal_number) . ')/', $string, $matches_day);
            if ($matches_day && $matches_day[1])
                $day = $matches_day[1];

            $temp = getdate();
            $day = $temp['mday'];
        }

        // Match Year if not already setted:
        if (empty($year)) {
            preg_match('/[0-9]{4}/', $string, $matches_year);
            if ($matches_year && $matches_year[0])
                $year = $matches_year[0];


            $temp = getdate();
            $year = $temp['year'];
        }
        if (!empty ($day) && !empty ($month) && empty($year)) {
            preg_match('/[0-9]{2}/', $string, $matches_year);
            if ($matches_year && $matches_year[0])
                $year = $matches_year[0];
        }

        // Day leading 0
        if (1 == strlen($day))
            $day = '0' . $day;

        // Month leading 0
        if (1 == strlen($month))
            $month = '0' . $month;

        // Check year:
        if (2 == strlen($year) && $year > 20)
            $year = '19' . $year;
        else if (2 == strlen($year) && $year < 20)
            $year = '20' . $year;

        $date = $month . "/" . $day . "/" . $year;


        // Return false if nothing found:
        if (empty($year) && empty($month) && empty($day))
            return false;
        else
            return $date;
    }


    public function mapping_usps($string)
    {
        if ($string == null) {
            $delivery_status = 10;

        } elseif (preg_match('/\bOut for Delivery\b/', $string) == true) {

            $delivery_status = 3;

        } elseif (preg_match('/\bReminder to Schedule Redelivery\b/', $string) == true) {
            $delivery_status = 4;

        } elseif (preg_match('/\bYour item was delivered\b/', $string) == true) {
            $delivery_status = 5;

        } elseif ((preg_match('/\bAvailable for Pickup\b/', $string) == true)) {
            $delivery_status = 3;
        } else {
            $delivery_status = 2;
        }
        return $delivery_status;

    }

    public function mapping_dhl($statusCode, $status)
    {
        if ($statusCode == null) {
            $delivery_status = 10;

        } elseif ($statusCode == 'pre-transit' && $status == 'PARTNER EVENT - SHIPMENT DEPARTED ORIGIN FACILITY') {
            $delivery_status = 2;

        } elseif ($statusCode == 'pre-transit') {
            $delivery_status = 1;
        } elseif ($statusCode == 'transit' && $status == 'Out for Delivery') {
            $delivery_status = 3;
        } elseif ($statusCode == 'transit' && $status == 'PROCESSED THROUGH SORT FACILITY'
            || $status == 'RESCHEDULED'
            || $status == 'FORWARDED') {
            $delivery_status = 4;

        } elseif ($statusCode == 'failure') {
            $delivery_status = 4;
        } elseif ($statusCode == 'delivered') {
            $delivery_status = 5;
        } else {
            $delivery_status = 2;
        }
        return $delivery_status;
    }

    public function mapping_yunexpress($TrackingStatus, $ProcessContent)
    {

        if ($TrackingStatus == null) {
            $delivery_status = 10;

        } elseif ($TrackingStatus == 10) {
            $delivery_status = 1;

        } elseif ($TrackingStatus == 20 && $ProcessContent == 'Out for Delivery') {
            $delivery_status = 3;

        } elseif ($TrackingStatus == 50) {
            $delivery_status = 5;
        } elseif ($TrackingStatus == 90 || (preg_match('/\bReturning package to shipper\b/', $ProcessContent)) == true) {
            $delivery_status = 4;
        } else {
            $delivery_status = 2;
        }

        return $delivery_status;
    }

    public
    function mapping_fedex($statusCD)
    {

        if ($statusCD == null) {
            $delivery_status = 10;

        } elseif ($statusCD == 'DL') {
            $delivery_status = 5;

        } elseif ($statusCD == 'DE' || $statusCD == 'SE' || $statusCD == 'RS') {
            $delivery_status = 4;

        } elseif ($statusCD == 'OD') {
            $delivery_status = 3;

        } elseif ($statusCD == 'OC') {
            $delivery_status = 1;
        } else {
            $delivery_status = 2;
        }

        return $delivery_status;

    }

    public
    function mapping_ups($type, $description)
    {

        if ($type == 'M' || $type == 'P') {
            $delivery_status = 1;
        } else if ($type == 'I' && (preg_match('/\bReminder to Schedule Redelivery\b/', $description)) == true) {
            $delivery_status = 3;
        } elseif ($type == 'I') {
            $delivery_status = 1;
        } elseif ($type == 'D') {
            $delivery_status = 5;
        } elseif ($type == 'X') {
            $delivery_status = 2;
        } else {
            $delivery_status = 10;
        }

        return $delivery_status;

    }

    public
    function mapping_yanwen($tracking_status, $message)
    {

        if ($tracking_status == 'PU10') {
            $delivery_status = 1;
        } elseif ($tracking_status == 'OTHER' && (preg_match('/\bOut for Delivery\b/', $message)) == true) {
            $delivery_status = 3;
        } elseif ((preg_match('/\bOut for Delivery\b/', $message)) == true) {
            $delivery_status = 3;
        } elseif ($tracking_status == 'LM50' || $message == 'Delivered') {
            $delivery_status = 5;
        } elseif ((preg_match('/\bReturning package to shipper\b/', $message)) == true
            || (preg_match('/\bATTEMPTED\b/', $message)) == true) {
            $delivery_status = 4;
        } else {
            $delivery_status = 2;
        }

        return $delivery_status;

    }

    public
    function count_array($tracking_number)
    {
        $array = Detail::where('tracking_number', $tracking_number)->get();

        return count($array);
    }

    public function call_yun($tracking_number)
    {

        $curl = curl_init();//Tạo curl

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://shipping.esrax.com/yunexpress/?trackingnumber=" . $tracking_number,
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
        $json = json_decode($response); //Chuyển dữ liệu trả về theo dạng json

        return $json;
    }

    public function handling_yun($json, $tracking_number, $id)
    {

        if (isset($json->Item)) { //Kiểm tra biến Item có tồn tại
            $item = $json->Item;

            $i = 1;//Tạo biến đếm
            if (isset($item->OrderTrackingDetails)) { //Kiểm tra biến OrderTrackingDetails có tồn tại

                $OrderTrackingDetails = $item->OrderTrackingDetails;

//                    $array_OrderTrackingDetails = count($OrderTrackingDetails);//Đếm số lượng phần tử mảng $OrderTrackingDetails

//                    $count_tracking = $this->count_array($tracking_number);//Đếm số lượng data trong table Detail theo tracking_number

//                    if ($array_OrderTrackingDetails > $count_tracking) {
                Detail::where('tracking_number', $tracking_number)->delete();
//                    Detail::where('tracking_number', $tracking_number)->where('step_number', 0)->update(['step_number' => $count_tracking]);
                foreach ($item->OrderTrackingDetails as $value) {

//                            if ($i > $count_tracking && $i <= $array_OrderTrackingDetails) {

                    $delivery_status = $this->mapping_yunexpress($value->TrackingStatus, $value->ProcessContent);//Mapping dữ liệu sang delivery_status table

                    $time = strtotime($value->ProcessDate);

                    $newformat = date('yy/m/d', $time);//format day

                    $today = strtotime(date("yy/m/d"));

                    $datediff = abs($time - $today);
                    $nam = floor($datediff / (365 * 60 * 60 * 24));
                    $thang = floor(($datediff - $nam * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                    $ngay = (floor(($datediff - $nam * 365 * 60 * 60 * 24 - $thang * 30 * 60 * 60 * 24) / (60 * 60 * 24)));

                    $form_data = array(
                        'tracking_id' => $id,
                        'tracking_number' => $tracking_number,
                        'process_content' => $value->ProcessContent,
                        'process_date' => $newformat,
                        'tracking_status' => $value->TrackingStatus,
                        'step_number' => $i,
                        'delivery_status' => $delivery_status,
                        'total_day' => $ngay
                    );
                    $i++;
                    $last_point = Detail::create($form_data);
                    //Log::channel('tracking_history')->info($response);
                }

            }

            Detail::whereId($last_point->id)->update(['step_number' => 0]);//Lưu bước cuối để query hiển thị ra view

            var_dump("Yun express : True");
//                    }
            Tracking::where('tracking_number', $tracking_number)->update(['approved' => 1]);//cập nhật để không quét lại
        } else {

            $check = Detail::where('tracking_number', $tracking_number)->get()->toArray();//Query dữ liệu

            if (empty($check)) {//Kiểm tra dữ liệu đã có trên Detail table chưa!
                $date = date('yy/m/d');
                $form_data = array(
                    'tracking_number' => $tracking_number,
                    'process_content' => 'No data found',
                    'process_date' => $date,
                    'step_number' => 0,
                    'delivery_status' => 10
                );

                $last_point = Detail::create($form_data);
                var_dump("Yun express : False");

            }

        }
    }

    public function call_dhl($tracking_number)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://shipping.esrax.com/dhl/?trackingNumber=" . $tracking_number,
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

    public function handling_dhl($json, $tracking_number, $id)
    {

        if (isset($json->shipments[0])) {
            $shipments = $json->shipments[0];

            if (isset($shipments->events)) {
                $events = $shipments->events;
                if (isset($events[0]->statusCode)) {

                    Detail::where('tracking_number', $tracking_number)->delete();
                    $i = 0;

                    foreach ($events as $value) {

                        $delivery_status = $this->mapping_dhl($value->statusCode, $value->status);

                        $time = strtotime($value->timestamp);

                        $date = date('yy/m/d', $time);


                        $today = strtotime(date("yy/m/d"));

                        $datediff = abs($time - $today);

                        $form_data = array(
                            'tracking_id' => $id,
                            'tracking_number' => $tracking_number,
                            'process_content' => $value->status,
                            'process_date' => $date,
                            'tracking_status' => $value->statusCode,
                            'step_number' => $i,
                            'delivery_status' => $delivery_status,
                            'total_day' => floor($datediff / (60 * 60 * 24))
                        );
                        $last_point = Detail::create($form_data);
                        $i++;
                    }
                    //Log::channel('tracking_history')->info($response);
                    var_dump("DHL eCommerce : True");

                } else {

                    Detail::where('tracking_number', $tracking_number)->delete();
                    $i = 0;

                    foreach ($events as $value) {

                        if ($value->description == null) {
                            $delivery_status = 10;

                        } elseif ($value->description == 'With delivery courier') {
                            $delivery_status = 3;

                        } elseif ($value->description == 'Forwarded for delivery') {
                            $delivery_status = 4;
                        } elseif ($value->description == 'Shipment on hold') {
                            $delivery_status = 7;
                        } elseif ($value->description == 'Delivered') {
                            $delivery_status = 5;
                        } elseif ($value->description == 'Shipment information received') {
                            $delivery_status = 1;
                        } else {
                            $delivery_status = 2;
                        }

                        $time = strtotime($value->timestamp);

                        $date = date('yy/m/d', $time);

                        $today = strtotime(date("yy/m/d"));

                        $datediff = abs($time - $today);

                        $form_data = array(
                            'tracking_id' => $id,
                            'tracking_number' => $tracking_number,
                            'process_content' => $value->description,
                            'process_date' => $date,
                            'step_number' => $i,
                            'delivery_status' => $delivery_status,
                            'total_day' => floor($datediff / (60 * 60 * 24))
                        );
                        $last_point = Detail::create($form_data);
                        $i++;
                        //Log::channel('tracking_history')->info($response);
                        var_dump("DHL : True");
                    }
                }
            }
            Tracking::where('tracking_number', $tracking_number)->update(['approved' => 1]);
        } else {

            $check = Detail::where('tracking_number', $tracking_number)->get()->toArray();//Query dữ liệu

            if (empty($check)) {
                $date = date('yy/m/d');
                $form_data = array(
                    'tracking_id' => $id,
                    'tracking_number' => $tracking_number,
                    'process_content' => $json->detail,
                    'process_date' => $date,
                    'step_number' => 0,
                    'delivery_status' => 10
                );
                $last_point = Detail::create($form_data);
                //Log::channel('tracking_history')->info($response);
//                    Tracking::where('tracking_number', $tracking_number)->update(['approved' => 1]);
            }
            var_dump("DHL : False");
        }
    }

    public function call_usps($tracking_number){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://shipping.esrax.com/usps/?trackingnumber=" . $tracking_number,
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

    public function handling_usps($json, $tracking_number, $id){
        if (isset($json->TrackResponse)) {

            $TrackResponse = $json->TrackResponse;
            if (isset($TrackResponse->TrackInfo)) {
                $TrackInfo = $TrackResponse->TrackInfo;
                if (isset($TrackInfo->Error)) {

                    $Error = $TrackInfo->Error;
                    $date = date('yy/m/d');

                    Detail::where('tracking_number', $tracking_number)->delete();
                    $form_data = array(
                        'tracking_id' => $id,
                        'tracking_number' => $tracking_number,
                        'process_content' => $Error->Description,
                        'process_date' => $date,
                        'delivery_status' => 10
                    );
                    $last_point = Detail::create($form_data);
                    Detail::whereId($last_point->id)->update(['step_number' => 0]);
                    //Log::channel('tracking_history')->info($response);

                } elseif (isset($TrackInfo->TrackDetail)) {

                    $TrackDetail = $TrackInfo->TrackDetail;
                    if (is_array($TrackDetail) == true) {
                        $i = 0;
                        Detail::where('tracking_number', $tracking_number)->delete();
                        foreach ($TrackDetail as $value) {

                            $matches = $this->find_date($value);

                            $time = strtotime($matches);

                            $newformat = date('yy/m/d', $time);//format day

                            $today = strtotime(date("yy/m/d"));

                            $datediff = abs($time - $today);

                            $delivery_status = $this->mapping_usps($value);//mapping delivery status

                            $form_data = array(
                                'tracking_id' => $id,
                                'tracking_number' => $tracking_number,
                                'process_content' => $value,
                                'process_date' => $newformat,
                                'delivery_status' => $delivery_status,
                                'step_number' => $i,
                                'total_day' => floor($datediff / (60 * 60 * 24))
                            );


                            $last_point = Detail::create($form_data);

                            $i++;
                            //Log::channel('tracking_history')->info($response);

                        }
                        if (isset($TrackInfo->TrackSummary)) {
                            Detail::where('tracking_number', $tracking_number)->where('step_number', 0)
                                ->update(['step_number' => $i]);

                            $matches = $this->find_date($TrackInfo->TrackSummary);

                            $time = strtotime($matches);

                            $newformat = date('yy/m/d', $time);

                            $today = strtotime(date("yy/m/d"));

                            $datediff = abs($time - $today);

                            $delivery_status = $this->mapping_usps($TrackInfo->TrackSummary);

                            $form_data = array(
                                'tracking_id' => $id,
                                'tracking_number' => $tracking_number,
                                'process_content' => $TrackInfo->TrackSummary,
                                'process_date' => $newformat,
                                'delivery_status' => $delivery_status,
                                'step_number' => 0,
                                'total_day' => floor($datediff / (60 * 60 * 24))
                            );
                            $last_point = Detail::create($form_data);
                            //Log::channel('tracking_history')->info($response);
                        }
                    } else {

                        $check = Detail::where('tracking_number', $tracking_number)->get()->toArray();//Query dữ liệu

                        if (empty($check)) {

                            $i = 0;

                            $matches = $this->find_date($TrackInfo->TrackSummary);

                            $time = strtotime($matches);

                            $newformat = date('yy/m/d', $time);

                            $today = strtotime(date("yy/m/d"));

                            $datediff = abs($time - $today);

                            $delivery_status = $this->mapping_usps($TrackInfo->TrackSummary);

                            $form_data = array(
                                'tracking_id' => $id,
                                'tracking_number' => $tracking_number,
                                'process_content' => $TrackDetail,
                                'step_number' => $i,
                                'delivery_status' => $delivery_status,
                                'process_date' => $newformat,
                                'total_day' => floor($datediff / (60 * 60 * 24))
                            );

                            $last_point = Detail::create($form_data);
                            $i++;
                            Detail::whereId($last_point->id)->update(['step_number' => 0]);

                            //Log::channel('tracking_history')->info($response);
                        }

                    }

                } elseif (isset($TrackInfo->TrackSummary)) {

                    Detail::where('tracking_number', $tracking_number)->delete();

                    $matches = $this->find_date($TrackInfo->TrackSummary);

                    $time = strtotime($matches);

                    $newformat = date('yy/m/d', $time);

                    $today = strtotime(date("yy/m/d"));

                    $datediff = abs($time - $today);

                    $delivery_status = $this->mapping_usps($TrackInfo->TrackSummary);

                    $form_data = array(
                        'tracking_id' => $id,
                        'tracking_number' => $tracking_number,
                        'process_content' => $TrackInfo->TrackSummary,
                        'process_date' => $newformat,
                        'delivery_status' => $delivery_status,
                        'step_number' => 0,
                        'total_day' => floor($datediff / (60 * 60 * 24))
                    );
                    $last_point = Detail::create($form_data);
                    //Log::channel('tracking_history')->info($response);
                }
            }
            Tracking::where('tracking_number', $tracking_number)->update(['approved' => 1]);
            var_dump('USPS complete');
        }
    }

    public function call_yanwen($tracking_number){

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://shipping.esrax.com/yw/?trackingnumber=". $tracking_number,
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

    public function handling_yanwen($json, $tracking_number, $id){
        if ($json != null) {
            if ($json->result == null) {

                $date = date('yy/m/d');

                $form_data = array(
                    'tracking_number' => $tracking_number,
                    'process_content' => $json->message,
                    'process_date' => $date,
                    'step_number' => 0,
                    'delivery_status' => 10,
                );

                Detail::create($form_data);
                var_dump("Yanwen: Null");
            } else {
                if (isset($json->result[0]->exchange_number)) {
                    if ($json->result[0]->exchange_number != "") {
                        $id_data = array(
                            'id' => $id
                        );
                        $data = array(
                            'courier' => 'USPS',
                            'tracking_number' => $json->result[0]->exchange_number
                        );

                        Tracking::updateOrCreate($id_data, $data);

                        var_dump("Update Tracking");

                        $new_tracking_number = $json->result[0]->exchange_number;

                        var_dump($new_tracking_number);
                        Detail::where('tracking_id', $id)->delete();
                        $new_json = $this->call_usps($new_tracking_number);

                        $form_data = $this->handling_usps($new_json, $new_tracking_number, $id);


                    }
                } else {
                    if ($json->result[0]->checkpoints != null) {

                        $checkpoints = $json->result[0]->checkpoints;
                        $step_number = 1;
                        Detail::where('tracking_number', $tracking_number)->delete();
                        foreach ($checkpoints as $value) {

                            $delivery_status = $this->mapping_yanwen($value->tracking_status, $value->message);

                            $time = strtotime($value->time_stamp);

                            $newformat = date('yy/m/d', $time);

                            $today = strtotime(date("yy/m/d"));

                            $datediff = abs($time - $today);

                            $form_data = array(
                                'tracking_id' => $id,
                                'tracking_number' => $tracking_number,
                                'process_content' => $value->message,
                                'process_date' => $newformat,
                                'tracking_status' => $value->tracking_status,
                                'step_number' => $step_number,
                                'delivery_status' => $delivery_status,
                                'total_day' => floor($datediff / (60 * 60 * 24))
                            );

                            Detail::create($form_data);
                            $step_number++;
                        }

                        Detail::where('tracking_number', $tracking_number)
                            ->where('tracking_status', $json->result[0]->tracking_status)
                            ->update(['step_number' => 0], ['tracking_id' => $id]);

                        Tracking::where('tracking_number', $tracking_number)->update(['approved' => 1]);
                        var_dump("Yanwen: Complete");
                    } else {
                        Detail::where('tracking_number', $tracking_number)->delete();
                        $date = date('yy/m/d');


                        $form_data = array(
                            'tracking_id' => $id,
                            'tracking_number' => $tracking_number,
                            'process_content' => $json->result[0]->tracking_status,
                            'process_date' => $date,
                            'tracking_status' => $json->result[0]->tracking_status,
                            'step_number' => 0,
                            'delivery_status' => 10,
                        );

                        Detail::create($form_data);
                        var_dump("Yanwen: False");
                    }
                }
            }
        }
    }

    public function call_fedex($tracking_number){

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://shipping.esrax.com/fedex/?trackingnumber=" . $tracking_number,
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

    public function handling_fedex($json, $tracking_number, $id){

        if (isset($json->TrackPackagesResponse)) {
            $TrackPackagesResponse = $json->TrackPackagesResponse;
            if (isset($TrackPackagesResponse->packageList[0])) {
                $packageList = $TrackPackagesResponse->packageList[0];
                if (isset($packageList->scanEventList)) {
                    $scanEventList = $packageList->scanEventList;
                    $i = 0;
                    Detail::where('tracking_number', $tracking_number)->delete();
                    foreach ($scanEventList as $value) {
                        if($value->status == ""){
                            $today = date("yy/m/d");

                            $form_data = array(
                                'tracking_id' => $id,
                                'tracking_number' => $tracking_number,
                                'process_content' => $value->status . " " . $value->scanDetails,
                                'process_date' => $today,
                                'tracking_status' => $value->statusCD,
                                'step_number' => $i,
                                'delivery_status' => 10,
                            );

                            $last_point = Detail::create($form_data);
                        }else {

                            $delivery_status = $this->mapping_fedex($value->statusCD);

                            $time = strtotime($value->date);

                            $today = strtotime(date("yy/m/d"));

                            $newformat = date('yy/m/d', $time);//format day

                            $datediff = abs($time - $today);

                            $form_data = array(
                                'tracking_id' => $id,
                                'tracking_number' => $tracking_number,
                                'process_content' => $value->status . " " . $value->scanDetails,
                                'process_date' => $newformat,
                                'tracking_status' => $value->statusCD,
                                'step_number' => $i,
                                'delivery_status' => $delivery_status,
                                'total_day' => floor($datediff / (60 * 60 * 24))
                            );

                            $last_point = Detail::create($form_data);
                            $i++;
                            //Log::channel('tracking_history')->info($response);
                            Tracking::where('tracking_number', $tracking_number)->update(['approved' => 1]);
                            var_dump("Fedex : True");
                        }
                    }
                }
            }
        } else {
            $check = Detail::where('tracking_number', $tracking_number)->get()->toArray();//Query dữ liệu
            if (empty($check)) {
                $date = date('yy/m/d');
                $form_data = array(
                    'tracking_number' => $tracking_number,
                    'process_content' => 'No data found',
                    'process_date' => $date,
                    'step_number' => 0,
                    'delivery_status' => 10
                );

                $last_point = Detail::create($form_data);
                var_dump("Fedex : False");

            }
        }

    }

    public function call_ups($tracking_number){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://shipping.esrax.com/ups/?trackingnumber=" . $tracking_number,
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

    public function handling_ups($json, $tracking_number,  $id){
        $shipment = $json->trackResponse->shipment;

        if (isset($shipment[0]->package)) {
            $package = $shipment[0]->package;

            $activity = $package[0]->activity;

            $step_number = 0;

            Detail::where('tracking_number', $tracking_number)->delete();

            foreach ($activity as $value) {

                $delivery_status = $this->mapping_ups($value->status->type, $value->status->description);

                $time = strtotime($value->date);

                $newformat = date('yy/m/d', $time);

                $today = strtotime(date("yy/m/d"));

                $datediff = abs($time - $today);

                $form_data = array(
                    'tracking_id' => $id,
                    'tracking_number' => $tracking_number,
                    'process_content' => $value->status->description,
                    'process_date' => $newformat,
                    'tracking_status' => $value->status->type,
                    'step_number' => $step_number,
                    'delivery_status' => $delivery_status,
                    'total_day' => floor($datediff / (60 * 60 * 24))
                );

                Detail::create($form_data);
                $step_number++;
            }

            var_dump("Complete");
            Tracking::where('tracking_number', $tracking_number)->update(['approved' => 1]);

        } else {

            if (isset($json->response->errors)) {

                $date = date('yy/m/d');

                $form_data = array(
                    'tracking_number' => $tracking_number,
                    'process_content' => $json->response->errors[0]->message,
                    'process_date' => $date,
                    'step_number' => 0,
                    'delivery_status' => 10
                );

                Detail::create($form_data);

            } else {

                $date = date('yy/m/d');

                $form_data = array(
                    'tracking_number' => $tracking_number,
                    'process_content' => $json->trackResponse->shipment[0]->warnings[0]->message,
                    'process_date' => $date,
                    'step_number' => 0,
                    'delivery_status' => 10
                );

                Detail::create($form_data);

            }

        }
    }

    public function UpdateTracking(Request $request)
    {
        $tracking_number = $request->tracking;
        $id = $request->id;

        if ($request->courier == 'Yun Express') {


            $curl = curl_init();//Tạo curl

            $json = $this->call_yun($tracking_number);

            $form_data = $this->handling_yun($json, $tracking_number, $id);

        } elseif ($request->courier == 'DHL' || $request->courier == 'DHL eCommerce') {
            //DHL=====================================================================

            $json = $this->call_dhl($tracking_number);

            $form_data = $this->handling_dhl($json, $tracking_number, $id);

        } elseif ($request->courier == 'Fedex') {
            //Fedex=====================================================================
//
            $json = $this->call_fedex($tracking_number);

            $form_data = $this->handling_fedex($json, $tracking_number, $id);

        } elseif ($request->courier == 'USPS' || $request->courier == 'Epacket' || $request->courier == 'China Post') {

            //USPS=====================================================================

            $json = $this->call_usps($tracking_number);

            $form_data = $this->handling_usps($json, $tracking_number, $id);

        }elseif ($request->courier == 'UPS'){

            $json = $this->call_ups($tracking_number);

            $form_data = $this->handling_ups($json, $tracking_number, $id);

        }elseif ($request->courier == 'YANWEN'){

            $json = $this->call_yanwen($tracking_number);

            $form_data = $this->handling_yanwen($json, $tracking_number, $id);
        }

        return back();
    }
}
