<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenancePurchaseOrder extends Model
{
    protected $table = 'maintenance_purchase_orders';
    
    protected $fillable = [
        'po_number',
        'task_id',
        'supplier_id',
        'status',
        'total_amount',
        'notes',
        'created_by',
        'updated_by',
        'gm_finance_approval',
        'gm_finance_approval_date',
        'gm_finance_approval_by',
        'gm_finance_approval_notes',
        'managing_director_approval',
        'managing_director_approval_date',
        'managing_director_approval_by',
        'managing_director_approval_notes',
        'president_director_approval',
        'president_director_approval_date',
        'president_director_approval_by',
        'president_director_approval_notes',
        'invoice_number',
        'invoice_date',
        'invoice_file_path',
        'receive_date',
        'receive_notes',
        'receive_photos'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'approved_at' => 'datetime',
        'total_amount' => 'decimal:2'
    ];
    
    public function task()
    {
        return $this->belongsTo(MaintenanceTask::class, 'task_id');
    }
    
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    
    public function items()
    {
        return $this->hasMany(MaintenancePurchaseOrderItem::class, 'po_id');
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    public function gmFinanceApprover()
    {
        return $this->belongsTo(User::class, 'gm_finance_approval_by');
    }
    
    public function managingDirectorApprover()
    {
        return $this->belongsTo(User::class, 'managing_director_approval_by');
    }
    
    public function presidentDirectorApprover()
    {
        return $this->belongsTo(User::class, 'president_director_approval_by');
    }
    
    public function getTotalAmountAttribute($value)
    {
        return $this->items()->sum('subtotal');
    }
    
    public function getGmFinanceApproverNameAttribute()
    {
        return $this->gmFinanceApprover ? $this->gmFinanceApprover->nama_lengkap : null;
    }
    
    public function getManagingDirectorApproverNameAttribute()
    {
        return $this->managingDirectorApprover ? $this->managingDirectorApprover->nama_lengkap : null;
    }
    
    public function getPresidentDirectorApproverNameAttribute()
    {
        return $this->presidentDirectorApprover ? $this->presidentDirectorApprover->nama_lengkap : null;
    }
    
    public function getCreatorNameAttribute()
    {
        return $this->creator ? $this->creator->nama_lengkap : null;
    }
    
    public function getSupplierNameAttribute()
    {
        return $this->supplier ? $this->supplier->name : null;
    }
    
    public function scopeByTask($query, $taskId)
    {
        return $query->where('task_id', $taskId);
    }
    
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
