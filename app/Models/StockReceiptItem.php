<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockReceiptItem extends Model
{
    protected $fillable = ['stock_receipt_id', 'ingredient_id', 'quantity', 'unit_cost', 'line_total'];

    protected $casts = [
        'quantity'   => 'decimal:3',
        'unit_cost'  => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function receipt()    { return $this->belongsTo(StockReceipt::class, 'stock_receipt_id'); }
    public function ingredient() { return $this->belongsTo(Ingredient::class); }
}
