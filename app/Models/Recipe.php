<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Công thức định lượng của MỘT món ăn (định lượng tính cho MỘT phần ăn).
 * is_sample = true nghĩa là số liệu minh họa, cần nhà hàng xác nhận.
 */
class Recipe extends Model
{
    protected $fillable = ['menu_id', 'is_sample', 'note', 'created_by', 'updated_by'];

    protected $casts = [
        'is_sample' => 'boolean',
    ];

    public function menu()    { return $this->belongsTo(Menu::class); }
    public function items()   { return $this->hasMany(RecipeItem::class)->orderBy('id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by'); }
}
