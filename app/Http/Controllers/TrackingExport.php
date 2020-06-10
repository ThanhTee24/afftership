<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Excel;
use App\Tracking;


class TrackingExport extends Controller implements FromCollection, WithHeadings
{
    use Exportable;

    public function collection()
    {
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
            ->orderBy('tracking.id')
            ->get();


        foreach ($data as $key => $row) {
            $tracking_array[] = array(
                '0' => $row->id,
                '1' => $row->order_date,
                '2' => $row->order_id,
                '3' => $row->courier,
                '4' => $row->tracking_number,
                '5' => $row->tracking_date,
                '6' => $row->count_day,
                '7' => $row->supplier,
                '8' => $row->status,
                '9' => $row->process_content,
                '10' => $row->process_date,
                '11' => $row->total,
            );
        }


        return (collect($tracking_array));
    }

    public function headings(): array
    {
        return [
            'ID',
            'Order date',
            'Order ID',
            'Courier',
            'Tracking number',
            'Tracking date',
            'Count day',
            'Supplier',
            'Status',
            'Detail',
            'Date',
            'Total day'
        ];
    }

    public function export()
    {
        return Excel::download(new TrackingExport(), 'Tracking.xlsx');


    }
}
