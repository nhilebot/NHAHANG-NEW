<?php

namespace App\Models;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image'];

   public function products()
{
    return $this->hasMany(Product::class, 'category_id');
}
  public function menus()
    {
        return $this->hasMany(Menu::class);
    }
}