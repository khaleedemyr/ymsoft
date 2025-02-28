<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SalesImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $rows)
    {
        return $rows;
    }

    public function rules(): array
    {
        return [
            'customer' => 'required',
            'invoice_document' => 'required',
            'sales_date' => 'required',
            'delivery_number' => 'required',
            'sub_category' => 'required',
            'items' => 'required',
            'inv_qty' => 'required|numeric',
            'uom' => 'required'
        ];
    }
}
