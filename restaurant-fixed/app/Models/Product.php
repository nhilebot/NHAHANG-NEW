<?php
// XÓA TẤT CẢ - DÁN TOÀN BỘ
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    
    protected $table = 'menus'; // 🔥 DÙNG table menus
    protected $fillable = ['name', 'price', 'image', 'description', 'stock', 'status', 'category_id'];
    protected $casts = ['price' => 'decimal:0'];

    public function category() { return $this->belongsTo(Category::class, 'category_id'); }
    public function orderItems() { return $this->hasMany(OrderItem::class, 'menu_id'); }
}