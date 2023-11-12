<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductTieredPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()->get();

        $products->each(function ($product) {
            $product->sku = json_decode($product->sku, 1);
        });

        $sku_attribute = DB::table('sku_attribute')->where('attr_name', '材质')->first();
        $sku_attribute->attr_value = json_decode($sku_attribute->attr_value, 1);

        return view('index', compact('products', 'sku_attribute'));
    }

    public function calcPrice(Request $request, Product $product)
    {
        $request->validate([
            'attr' => 'required',
            'length' => 'required',
            'width' => 'required',
            'height' => 'required',
            'qty' => 'required',
            'profit' => 'sometimes',
        ]);

        $total_square = number_format((($request->length + $request->width) * ($request->width + $request->height)) * ($request->qty / $product->per_bundle_qty) * $product->thickness / 100 / 10000, 3);

        $score = (($request->length + $request->width) * 2 + 3) * ($request->width + $request->height + 0.3) / 10000;

        $sku = json_decode($product->sku, 1);

        $skus = [];
        foreach ($sku['sku'] as $item) {
            $skus[$item['材质']] = $this->getTieredPrices($item);
        }

        if (!isset($skus[$request->attr])) {
            return response()->json(['status' => false, 'error' => '不支持的材质']);
        }
        $price = $skus[$request->attr];
        $tiered_price = 0;
        $used_square = $score * $request->qty;
        foreach ($price as $cond) {
            if ($cond['gt'] < $used_square && $cond['lte'] >= $used_square) {
                $tiered_price = $cond['price'];
            }
        }
        if (empty($tiered_price)) {
            return response()->json(['status' => false, 'error' => '未设置阶梯价']);
        }

        // $match_price = ProductTieredPrice::query()->where('product_id', $product->id)->where('gt', '<', $request->qty)->where('elt', '>=', $request->qty)->first();
        // if (!$match_price) {
        //     return response()->json(['status' => false, 'error' => '未设置阶梯价']);
        // }

        $per_price = $score * $tiered_price;

        $per_price_by_profit = 0;
        if (!empty($request->profit)) {
            $per_price_by_profit = $per_price * (1 + $request->profit / 100);
        }

        // $total_price = number_format($per_price * $request->qty, 3);
        $total_weight = (($request->length + $request->width) * ($request->width + $request->height)) * ($request->qty / $product->per_bundle_qty) * $product->thickness / $product->jettison_ratio;

        return response()->json([
            'status' => true,
            'score' => $score,
            'tiered_price' => $tiered_price,
            'per_price' => number_format($per_price, 3),
            'per_price_by_profit' => number_format($per_price_by_profit, 3),
            'total_square' => $total_square,
            'total_weight' => $total_weight,
        ]);
    }

    private function getTieredPrices($origin)
    {
        $prices = [];
        foreach ($origin as $key => $val) {
            if (false === strpos($key, 'tieredPrice')) {
                continue;
            }
            list(, $gt, $lte) = explode('_', $key);
            $prices[] = [
                'gt' => $gt,
                'lte' => $lte,
                'price' => $val,
            ];
        }
        return $prices;
    }
}
