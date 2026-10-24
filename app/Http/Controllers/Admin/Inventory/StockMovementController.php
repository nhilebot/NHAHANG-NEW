<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $movements = StockMovement::with(['ingredient', 'user', 'reference'])
            ->when($request->filled('ingredient_id'), fn ($q) => $q->where('ingredient_id', $request->ingredient_id))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest('id')
            ->paginate(25)->withQueryString();

        return view('admin.inventory.movements.index', [
            'movements'   => $movements,
            'ingredients' => Ingredient::orderBy('name')->get(['id', 'code', 'name']),
        ]);
    }
}
