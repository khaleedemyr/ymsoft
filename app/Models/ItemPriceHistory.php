<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPriceHistory extends Model
{
    use HasFactory;
    
    // Nama tabel yang digunakan oleh model ini
    protected $table = 'item_prices_history';
    
    // Kolom yang dapat diisi (fillable)
    protected $fillable = [
        'item_id',
        'supplier_id',
        'price',
        'price_date',
        'purchase_order_id'
    ];
    
    // Relationship dengan Item
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    
    // Relationship dengan Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    
    // Relationship dengan Purchase Order
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
