<?php

namespace App\Console\Commands;

use App\Model\Detail;
use App\Model\Tracking;
use Illuminate\Console\Command;

class ConvertCourier extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Get:Convert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert DHL eCommerce to USPS';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

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

    public function call_usps($tracking_number)
    {
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

    public function convertDhltoUsps($json, $tracking_number, $id)
    {
        if (isset($json->TrackResponse)) {
            $TrackResponse = $json->TrackResponse;
            if (isset($TrackResponse->TrackInfo)) {
                $TrackInfo = $TrackResponse->TrackInfo;
                if (isset($TrackInfo->Error)) {
                    var_dump("Not yet");

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

                            var_dump($form_data);
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
                            var_dump($form_data);
                            $last_point = Detail::create($form_data);
                            //Log::channel('tracking_history')->info($response);
                        }
                        Tracking::where('tracking_number', $tracking_number)->update(['courier' => 'USPS']);
                        var_dump('Convert complete');
                    }

                }
            }

        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $dhl = Tracking::
//        where('courier', '=', 'DHL')
        where('courier', '=', 'DHL eCommerce')
            ->whereRaw('LENGTH(tracking_number) > ?', [12])
            ->select('id', 'tracking_number')
            ->get();

        foreach ($dhl as $value) {
            $tracking_number = $value->tracking_number;
            $id = $value->id;
            var_dump($tracking_number);

            $json = $this->call_usps($tracking_number);

            $form_data = $this->convertDhltoUsps($json, $tracking_number, $id);
        }
    }
}
