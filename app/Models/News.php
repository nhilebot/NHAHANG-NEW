<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    protected $table = 'news';

    protected $fillable = [
        'title', 'slug', 'category', 'excerpt', 'content',
        'image', 'is_published', 'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    // Các chuyên mục hiển thị ở thanh lọc
    public const CATEGORIES = ['Khuyến mãi', 'Sự kiện', 'Món mới', 'Tin nhà hàng'];

    // Chỉ lấy bài đã bật hiển thị và đã đến giờ đăng
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                     ->where(function ($q) {
                         $q->whereNull('published_at')->orWhere('published_at', '<=', now());
                     });
    }

    // Mô tả ngắn: ưu tiên cột excerpt, không có thì cắt từ nội dung
    public function getSummaryAttribute()
    {
        return $this->excerpt ?: Str::limit(strip_tags($this->content), 140);
    }

    // Ngày hiển thị: ưu tiên ngày đăng, không có thì ngày tạo
    public function getDisplayDateAttribute()
    {
        return ($this->published_at ?? $this->created_at)->format('d/m/Y');
    }
}
