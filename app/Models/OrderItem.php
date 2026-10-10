<?php
// THAY THẾ HOÀN TOÀN
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;
    
    protected $table = 'order_items';
    protected $fillable = ['order_id','reservation_id', 'menu_id', 'product_name', 'quantity', 'price', 'chef_status', 'is_extra'];
    protected $casts = ['price' => 'decimal:2', 'is_extra' => 'boolean'];

    public function order() { return $this->belongsTo(Order::class); }
    public function menu()
{
    return $this->belongsTo(Menu::class, 'menu_id');
}
    public function product() { return $this->belongsTo(Product::class, 'menu_id'); }
}