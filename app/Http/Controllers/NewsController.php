<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    // Trang danh sách: /news  (lọc: /news?category=Khuyến mãi)
    public function index(Request $request)
    {
        $category = $request->query('category');

        $query = News::published()->orderByDesc('published_at')->orderByDesc('id');

        if ($category && in_array($category, News::CATEGORIES, true)) {
            $query->where('category', $category);
        } else {
            $category = null;
        }

        $news = $query->paginate(7)->withQueryString();

        return view('news.index', [
            'news'       => $news,
            'category'   => $category,
            'categories' => News::CATEGORIES,
        ]);
    }

    // Trang chi tiết: /news/{slug}
    public function show($slug)
    {
        $news = News::published()->where('slug', $slug)->firstOrFail();

        $related = News::published()
            ->where('id', '!=', $news->id)
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        return view('news.show', compact('news', 'related'));
    }
}
