<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesHeader extends Model
{
    protected $fillable = [
        'customer_id',
        'invoice_document',
        'sales_date',
        'delivery_number',
        'total_amount',
        'status'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function details()
    {
        return $this->hasMany(SalesDetail::class);
    }
}

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesDetail extends Model
{
    protected $fillable = [
        'sales_header_id',
        'item_id',
        'sub_category_id',
        'quantity',
        'price',
        'amount'
    ];

    public function header()
    {
        return $this->belongsTo(SalesHeader::class, 'sales_header_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
}
