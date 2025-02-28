class SalesDetail extends Model
{
    protected $table = 'sales_details';
    
    protected $fillable = [
        'sales_header_id',
        'item_id',
        'sub_category_id',
        'quantity',
        'uom',
        'price',
        'amount'
    ];
} 