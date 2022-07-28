<?php


namespace App\Http\Controllers\Api;


use App\Business;
use App\Category;
use App\Transaction;
use Illuminate\Support\Facades\DB;

trait Common
{

    public function getTrendingProducts($business_id = 1, $filters = [])
    {
        $query = Transaction::join(
            'transaction_sell_lines as tsl',
            'transactions.id',
            '=',
            'tsl.transaction_id'
        )
            ->join('products as p', 'tsl.product_id', '=', 'p.id')
            ->join('brands as b', 'b.id', '=', 'p.brand_id')
            ->join('variations as v', 'tsl.product_id', '=', 'v.product_id')
            ->join('purchase_lines as pl', 'pl.product_id', '=', 'p.id')
            ->leftjoin('units as u', 'u.id', '=', 'p.unit_id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final');

        if (!empty($filters['location_id'])) {
            $query->where('transactions.location_id', $filters['location_id']);
        }
        if (!empty($filters['category'])) {
            $query->where('p.category_id', $filters['category']);
        }
        if (!empty($filters['sub_category'])) {
            $query->where('p.sub_category_id', $filters['sub_category']);
        }
        if (!empty($filters['brand'])) {
            $query->where('p.brand_id', $filters['brand']);
        }
        if (!empty($filters['keyword'])) {
            $query->where('p.name', 'like', '%' . $filters['keyword'] . '%');
            $query->orWhere('p.product_description', 'like', '%' . $filters['keyword'] . '%');
        }
        if (!empty($filters['unit'])) {
            $query->where('p.unit_id', $filters['unit']);
        }
        if (!empty($filters['limit'])) {
            $query->limit($filters['limit']);
        } else {
            $query->limit(5);
        }
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween(DB::raw('date(transaction_date)'), [$filters['start_date'],
                $filters['end_date']]);
        }
        if (!empty($filters['products'])) {
            $query->whereIn('p.id', $filters['products']);
        }

        if (!empty($filters['paginate'])) {
            $products = $query->select(
                DB::raw("(SUM(tsl.quantity) - COALESCE(SUM(tsl.quantity_returned), 0)) as total_unit_sold"),
                DB::raw("pl.quantity - pl.quantity_sold as stock_remaining"),
                'p.name as product',
                'u.short_name as unit',
                'tsl.product_id as id',
                'p.image',
                'v.sell_price_inc_tax',
                'b.name as brand_name'
            )
                ->groupBy('tsl.product_id')
                ->orderBy('total_unit_sold', 'desc')
                ->paginate($filters['paginate']);
            return $products;
        }

        $products = $query->select(
            DB::raw("(SUM(tsl.quantity) - COALESCE(SUM(tsl.quantity_returned), 0)) as total_unit_sold"),
            DB::raw("pl.quantity - pl.quantity_sold as stock_remaining"),
            'p.name as product',
            'u.short_name as unit',
            'tsl.product_id as id',
            'p.image',
            'v.sell_price_inc_tax',
            'b.name as brand_name'
        )
            ->groupBy('tsl.product_id')
            ->orderBy('total_unit_sold', 'desc')
            ->get();
        return $products;
    }

    public function getCategories()
    {
        return Category::limit('10')->get();
    }

    public function getBanners($business_id = 1)
    {
        return Business::find($business_id)->appBanners;
    }

    public function getLocation($business_id)
    {
        return Business::where('id', $business_id)->select('latitude', 'longitude', 'radius')->first();
    }
}