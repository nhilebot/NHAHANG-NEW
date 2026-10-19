<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngredientCategory extends Model
{
    protected $fillable = ['name', 'description'];

    public function ingredients()
    {
        return $this->hasMany(Ingredient::class, 'category_id');
    }
}