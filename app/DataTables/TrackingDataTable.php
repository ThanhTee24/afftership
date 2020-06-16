<?php

namespace App\DataTables;

use App\Tracking;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Html\Editor\Editor;

class TrackingDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return $this->datatables
            ->eloquent($this->query())
            ->make(true);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Tracking $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Tracking $model)
    {
        $query = $model->newQuery()
            ->leftJoin('detail', function ($join) {
                $join->on('tracking.id', '=', 'detail.tracking_id')
                    ->where('detail.step_number', '=', 0);
            })
            ->join('delivery_status', 'detail.delivery_status', 'delivery_status.id')
            ->select('tracking.id', 'tracking.order_date', 'tracking.order_id', 'tracking.courier',
                'tracking.tracking_number', 'tracking.tracking_date', 'tracking.paypal_account', 'tracking.update_status',
                'tracking.created_at', 'tracking.transaction_id',
                'detail.process_content', 'detail.process_date', 'delivery_status.name as status',
                'detail.total_day as total')
            ->get();

        return $query;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters([
                'dom' => '<"row mb-3"<"col-sm-12 col-md-12"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>><"row"<"col-sm-12 col-md-12"rt>><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                'stateSave' => true,
                'buttons' => [
                    ['extend' => 'create', 'className' => 'btn btn-default btn-sm no-corner',],
                    ['extend' => 'export', 'className' => 'btn btn-default btn-sm no-corner',],
                    ['extend' => 'print', 'className' => 'btn btn-default btn-sm no-corner',],
                    ['extend' => 'reset', 'className' => 'btn btn-default btn-sm no-corner',],
                    ['extend' => 'reload', 'className' => 'btn btn-default btn-sm no-corner',]
                ],
                'scrollX' => true
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'order_date' => ['title' => 'Order Date', 'data' => 'order_date'],
            'order_id' => ['title' => 'Order ID', 'data' => 'order_id'],
            'courier' => ['title' => 'Courier', 'data' => 'courier'],
            'tracking_number' => ['title' => 'Tracking number', 'data' => 'tracking_number'],
            'tracking_date' => ['title' => 'Tracking date', 'data' => 'tracking_date'],
            'count_day' => ['title' => 'Count day', 'data' => 'count_day'],
            'supplier' => ['title' => 'Supplier', 'data' => 'supplier'],
            'status' => ['title' => 'Status', 'data' => 'status'],
            'process_content' => ['title' => 'Detail', 'data' => 'process_content'],
            'process_date' => ['title' => 'Process date', 'data' => 'process_date'],
            'total' => ['title' => 'Total', 'data' => 'total'],
            'note' => ['title' => 'Note', 'data' => 'note'],
            'approved' => ['title' => '', 'data' => 'approved'],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Tracking_' . date('YmdHis');
    }
}
