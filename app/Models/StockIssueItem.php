<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockIssueItem extends Model
{
    protected $fillable = ['stock_issue_id', 'ingredient_id', 'quantity', 'unit_cost', 'line_total'];

    protected $casts = [
        'quantity'   => 'decimal:3',
        'unit_cost'  => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function issue()      { return $this->belongsTo(StockIssue::class, 'stock_issue_id'); }
    public function ingredient() { return $this->belongsTo(Ingredient::class); }
}
