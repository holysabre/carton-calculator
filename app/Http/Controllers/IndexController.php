<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductTieredPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()->with(['tiered_prices'])->get();

        return view('index', compact('products'));
    }

    public function calcPrice(Request $request, Product $product)
    {
        $request->validate([
            'length' => 'required',
            'width' => 'required',
            'height' => 'required',
            'qty' => 'required',
            'profit' => 'sometimes',
        ]);

        $total_square = number_format((($request->length + $request->width) * ($request->width + $request->height)) * ($request->qty / $product->per_bundle_qty) * $product->thickness / 100 / 10000, 3);

        $score = (($request->length + $request->width) * 2 + 2) * ($request->width + $request->height + 0.3) / 10000;

        $match_price = ProductTieredPrice::query()->where('product_id', $product->id)->where('gt', '<', $request->qty)->where('elt', '>=', $request->qty)->first();
        if (!$match_price) {
            return response()->json(['status' => false, 'error' => '未设置阶梯价']);
        }

        $per_price = $score * $match_price->price;

        $per_price_by_profit = 0;
        if (!empty($request->profit)) {
            $per_price_by_profit = $per_price * (1 + $request->profit / 100);
        }

        // $total_price = number_format($per_price * $request->qty, 3);
        $total_weight = (($request->length + $request->width) * ($request->width + $request->height)) * ($request->qty / $product->per_bundle_qty) * $product->thickness / $product->jettison_ratio;

        return response()->json([
            'status' => true,
            'per_price' => number_format($per_price, 3),
            'per_price_by_profit' => number_format($per_price_by_profit, 3),
            'total_square' => $total_square,
            'total_weight' => $total_weight,
        ]);
    }
}
