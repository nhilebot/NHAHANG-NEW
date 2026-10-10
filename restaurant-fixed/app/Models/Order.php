<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

protected $fillable = [
    'code', 'user_id', 'reservation_id', 'table_number', 'total_price', 
    'status', 'payment_method', 'name', 'phone', 'notes'
];
protected $casts = [
    'total_price' => 'decimal:2',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function items()
{
    return $this->hasMany(\App\Models\OrderItem::class);
}
// Quan hệ với bảng Tables
public function table()
{
    return $this->belongsTo(Table::class, 'table_number', 'id');
}

// Giúp Blade hiển thị màu sắc dựa trên trạng thái
public function getStatusColorAttribute() {
    return match($this->status) {
        'pending' => 'bg-warning',
        'serving' => 'bg-info',
        'ready' => 'bg-success',
        'paid' => 'bg-primary',
        default => 'bg-secondary'
    };
}
}
