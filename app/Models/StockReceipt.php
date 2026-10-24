<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockReceipt extends Model
{
    protected $fillable = ['code', 'supplier_id', 'received_at', 'total_amount', 'note', 'created_by'];

    protected $casts = [
        'received_at'  => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function supplier()  { return $this->belongsTo(Supplier::class); }
    public function creator()   { return $this->belongsTo(User::class, 'created_by'); }
    public function items()     { return $this->hasMany(StockReceiptItem::class); }
    public function movements() { return $this->morphMany(StockMovement::class, 'reference'); }
}
