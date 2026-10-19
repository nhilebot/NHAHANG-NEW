<?php

namespace App\Models;

use App\Models\Concerns\GeneratesCode;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use GeneratesCode;

    protected $fillable = ['code', 'name', 'phone', 'email', 'address', 'note', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public static function newCode(): string
    {
        return static::nextCode('NCC', 3);
    }

    public function scopeSearch($q, ?string $keyword)
    {
        if ($keyword === null || trim($keyword) === '') {
            return $q;
        }
        $kw = '%' . trim($keyword) . '%';

        return $q->where(function ($w) use ($kw) {
            $w->where('code', 'like', $kw)
              ->orWhere('name', 'like', $kw)
              ->orWhere('phone', 'like', $kw)
              ->orWhere('email', 'like', $kw);
        });
    }
}