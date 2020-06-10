<?php

namespace App\Imports;

use App\Model\Tracking;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        return new Tracking([
            'order_date' => $row[0],
            'order_id' => $row[1],
            'paypal_account' => $row[2],
            'transaction_id' => $row[3],
            'courier' => $row[4],
            'tracking_number' => $row[5],
            'supplier' => $row[6],

        ]);
    }

}
