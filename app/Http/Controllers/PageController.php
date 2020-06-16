<?php

namespace App\Http\Controllers;

use App\Model\Crawl_bighub;
use App\Model\Detail;
use App\Model\List_supplier;
use App\Model\Tracking;
use Facades\App\Respository\Tracking_cache;
use App\Model\Delivery_status;
use Illuminate\Http\Request;
use Excel;
use App\Imports\TrackingImportFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use App\Export\TrackingExport;
use Validator;
use Response;
use App\DataTables\TrackingDataTable;


class PageController extends Controller
{

    public function index(TrackingDataTable $dataTable)
    {
        return $dataTable->render('datatables');
    }

    public function getPage()
    {

        $usps = Tracking::Where(function($query) {
            $query->where('courier', '=', 'USPS')
                ->orwhere('courier', '=', 'Epacket')
                ->orwhere('courier', '=', 'China Post');
        })
            ->where('approved', '=', null)
            ->select('id', 'tracking_number')->get();
        dd($usps);
        $list_supplier = List_supplier::all();
        return view('page', compact('list_supplier'));
    }

    public function getdata()
    {
        $start = request()->get('start');
        $length = request()->get('length');

        $data = DB::table('delivery_status')
            ->Join('detail', function ($join) {
                $join->on('delivery_status.id', '=', 'detail.delivery_status')
                    ->where('detail.step_number', '=', 0);
            })
            ->rightJoin('tracking', 'detail.tracking_number', '=', 'tracking.tracking_number')
            ->select('tracking.id', 'tracking.order_date', 'tracking.order_id', 'tracking.courier',
                'tracking.tracking_number', 'tracking.tracking_date', 'tracking.count_day', 'tracking.note', 'tracking.created_at',
                'tracking.supplier', 'tracking.approved',
                'detail.process_content', 'detail.process_date', 'delivery_status.name as status',
                'detail.total_day as total')
            ->orderBy('count_day', 'DESC')->take(20)
            ->get();

        return DataTables()::of($data)->addColumn('action', function ($value) {
            $button = '<div style="display: inline-block"> <a class="detail-modal btn btn-xs btn-clean btn-icon"
                                                      data-tracking_number="' . $value->tracking_number . '">
                                                      <i class="fa fa-search text-warning mr-5 icon-md"></i></a>';
            $button .= '&nbsp;&nbsp;';
            $button .= '<a class="edit-modal btn btn-xs btn-clean btn-icon" title="Edit" data-id="' . $value->id . '"
                           data-order_date="' . $value->order_date . '" data-order_id="' . $value->order_id . '"
                           data-courier="' . $value->courier . '" data-tracking_number="' . $value->tracking_number . '"
                           data-tracking_date="' . $value->tracking_date . '" data-count_day="' . $value->count_day . '"
                           data-status="' . $value->status . '"
                           data-content="' . ucwords(strtolower($value->process_content)) . '"
                           data-process_date="' . $value->process_date . '" data-total="' . $value->total . '"
                           data-note="' . $value->note . '">
                            <i class="flaticon2-pen icon-md text-danger"></i>
                        </a>';
            $button .= '&nbsp;&nbsp;';
            $button .= '<a class="update-modal btn btn-xs btn-clean btn-icon" title="Update" data-id="' . $value->id . '"
                           data-order_date="' . $value->order_date . '" data-order_id="' . $value->order_id . '"
                           data-courier="' . $value->courier . '" data-tracking_number="' . $value->tracking_number . '">
                            <i class="ki ki-round icon-md text-success"></i>
                        </a></div>';

            return $button;
        })
            ->rawColumns(['action'])
            ->make(true);


    }

    function csvToArray($filename = '', $delimiter = ',')
    {

        $file_handle = fopen($filename, 'r');

        while (!feof($file_handle)) {
            $data = fgetcsv($file_handle, 0, $delimiter);
            if ($data[5] != null) {

                if (strlen($data[0]) >= 11) {
                    $data[0] = substr($data[0], 3);
                }

                $order_id = array(
                    'order_id' => $data[1]
                );

                $time = strtotime($data[0] . "\n");
                $today = date("yy/m/d");

                if ($time == false) {
                    $form_data = array(
                        'order_date' => $data[0],
                        'paypal_account' => $data[2],
                        'transaction_id' => $data[3],
                        'courier' => $data[4],
                        'tracking_number' => $data[5],
                        'supplier' => $data[6],
                        'tracking_date' => $today
                    );
                } else {
                    $newformat = date('yy/m/d', $time);

                    $form_data = array(
                        'order_date' => $newformat,
                        'paypal_account' => $data[2],
                        'transaction_id' => $data[3],
                        'courier' => $data[4],
                        'tracking_number' => $data[5],
                        'supplier' => $data[6],
                        'tracking_date' => $today
                    );
                }

                Tracking::updateOrCreate($order_id, $form_data);

            }
        }
        fclose($file_handle);

        return 0;
    }

    public function import(Request $request)
    {
        $file = $request->file;
        $validator = Validator::make(
            [
                'file' => $file,
                'extension' => strtolower($file->getClientOriginalExtension()),
            ],
            [
                'file' => 'required',
                'extension' => 'required|in:csv',
            ]
        );
        if ($validator->passes()) {
            try {
                $file = request()->file('file');
                $customerArr = $this->csvToArray($file);
                return back();
            } catch (\Exception $e) {
                return "File bị lỗi";
            }

//        Excel::import(new TrackingImportFile, request()->file('file'));
        } else {
            return response()->json([
                'message' => $validator->errors()->all(),
                'message' => 'This not .csv file',
                'class_name' => 'alert-danger'
            ]);
        }
    }


    public function postEdit(Request $request)
    {

        $form_data = array(
            'order_date' => $request->order_date,
            'order_id' => $request->order_id,
            'courier' => $request->courier,
            'tracking_number' => $request->tracking_number,
            'tracking_date' => $request->tracking_date,
            'note' => $request->note
        );

        Tracking::whereId($request->id)->update($form_data);

        return response()->json(['success' => 'Data Added successfully.']);
    }

    public function Editpaypal(Request $request)
    {

        $from_data = array(
            'paypal_account' => $request->paypal_account,
            'transaction_id' => $request->transaction_id
        );

        Tracking::whereId($request->id)->update($from_data);

        return response()->json(['success' => 'Data Added successfully.']);
    }

    public function getPaypal_table()
    {

        return view('paypal');
    }

    public function getpaypal()
    {

//        $data = DB::table('tracking') ->leftJoin('detail', function ($join) {
//            $join->on('tracking.id', '=', 'detail.tracking_id')
//                ->where('detail.step_number', '=', 0);
//        })
//            ->leftJoin('delivery_status', 'detail.delivery_status', '=', 'delivery_status.id')
//            ->select('tracking.id', 'tracking.order_date', 'tracking.order_id', 'tracking.courier',
//                'tracking.tracking_number', 'tracking.tracking_date', 'tracking.paypal_account', 'tracking.update_status',
//                'tracking.created_at', 'tracking.transaction_id',
//                'detail.process_content', 'detail.process_date', 'delivery_status.name as status',
//                'detail.total_day as total')
//            ->get();

        $data = DB::table('delivery_status')
            ->Join('detail', function ($join) {
                $join->on('delivery_status.id', '=', 'detail.delivery_status')
                    ->where('detail.step_number', '=', 0);
            })
            ->rightJoin('tracking', 'detail.tracking_number', 'tracking.tracking_number')
            ->select('tracking.id', 'tracking.order_date', 'tracking.order_id', 'tracking.courier',
                'tracking.tracking_number', 'tracking.tracking_date', 'tracking.paypal_account', 'tracking.update_status',
                'tracking.created_at', 'tracking.transaction_id',
                'detail.process_content', 'detail.process_date', 'delivery_status.name as status',
                'detail.total_day as total')
            ->get();


        return DataTables()::of($data)->addColumn('action', function ($value) {
            $button = ' <a class="detail-modal btn btn-sm btn-clean btn-icon"
                                                      data-tracking_number="' . $value->tracking_number . '">
                                                      <i class="fa fa-search text-warning mr-5 icon-md"></i></a>';
            $button .= '&nbsp;&nbsp;';
            $button .= '<a class="edit-modal btn btn-sm btn-clean btn-icon" title="Edit" data-id="' . $value->id . '"
                           data-paypal_account="' . $value->paypal_account . '" data-transaction_id="' . $value->transaction_id . '">
                            <i class="flaticon2-pen icon-md text-danger"></i>
                        </a>';
            $button .= '&nbsp;&nbsp;';
            $button .= '<a class="add-modal btn btn-sm btn-clean btn-icon" title="Add" data-id="' . $value->id . '"
                           data-order_date="' . $value->order_date . '" data-order_id="' . $value->order_id . '"
                           data-courier="' . $value->courier . '" data-tracking_number="' . $value->tracking_number . '"
                           data-paypal_account="' . $value->paypal_account . '" data-transaction_id="' . $value->transaction_id . '">
                            <i class="ki ki-plus icon-lg text-success"></i>
                        </a>';
            $button .= '&nbsp;&nbsp;';
            $button .= '<a class="update-modal btn btn-sm btn-clean btn-icon" title="Update" data-id="' . $value->id . '"
                           data-order_date="' . $value->order_date . '" data-order_id="' . $value->order_id . '"
                           data-courier="' . $value->courier . '" data-tracking_number="' . $value->tracking_number . '"
                           data-paypal_account="' . $value->paypal_account . '" data-transaction_id="' . $value->transaction_id . '">
                            <i class="ki ki-round icon-lg text-success"></i>
                        </a>';
            return $button;
        })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function test()
    {
        return view('test');
    }


    public function Detail(Request $request)
    {

        $detail = Detail::where('tracking_number', $request->tracking_number)->orderBy('detail.process_date', 'desc')
            ->rightJoin('delivery_status', 'detail.delivery_status', '=', 'delivery_status.id')
            ->select('detail.process_content', 'detail.process_date', 'delivery_status.name as status')->get();

        $detail_0 = Detail::where('tracking_number', $request->tracking_number)
            ->where('step_number', 0)
            ->leftJoin('delivery_status', 'detail.delivery_status', '=', 'delivery_status.id')
            ->select('detail.process_content', 'detail.process_date', 'delivery_status.name as status')->get();
        $output_body = '';

//        foreach ($detail_0 as $value) {
//            $output_detail = '<tr>
//                            <td>' . $value->process_date . '</td>
//                            <td>' . $value->status . '</td>
//                            <td >' . $value->process_content . '</td>
//                       </tr>';
//        }
        foreach ($detail as $value) {
            $output_body .= '<tr>
                            <td>' . $value->process_date . '</td>
                            <td>' . $value->status . '</td>
                            <td >' . $value->process_content . '</td>
                       </tr>';
        }

        $data = '<tbody class="trackingdetail">
        ' . $output_body . '
        </tbody>';

        return response()->json($data);
    }

    public function export()
    {
        return Excel::download(new TrackingExport, 'Tracking.xlsx');
    }

    public function exportTracking(Request $request)
    {


        $rules = array(
            'to_date' => 'before:from_date',
            'from_date' => 'after:to_date',

        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {

            $to_date = $request->to_date;
            $from_date = $request->from_date;
            $supplier = $request->supplier;


            $data_export = DB::table('delivery_status')
                ->Join('detail', function ($join) {
                    $join->on('delivery_status.id', '=', 'detail.delivery_status')
                        ->where('detail.step_number', '=', 0);
                })
                ->rightJoin('tracking', 'detail.tracking_number', 'tracking.tracking_number')
                ->where('order_date', '>=', $to_date)
                ->where('order_date', '<=', $from_date)
                ->where('supplier', '=', $supplier)
                ->select('tracking.id', 'tracking.order_date', 'tracking.order_id', 'tracking.courier',
                    'tracking.tracking_number', 'tracking.tracking_date',
                    'tracking.created_at', 'tracking.supplier',
                    'detail.process_content', 'detail.process_date', 'delivery_status.name as status',
                    'detail.total_day as total')
                ->get();

            if ($data_export == null) {
                return "Không có dữ liệu export";
            } else {
                return $data_export;
            }
        } else {
            return $validator->errors();
        }
    }

}
