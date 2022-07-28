<?php


namespace App\Http\Controllers\Api;


use App\Category;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeScreenController extends ResponseController
{
    use Common;

    public function getHomeScreenData()
    {
        $result['categories'] = $this->getCategories();
        $result['products'] = $this->getTrendingProducts();
        $result['banners'] = $this->getBanners();
        $message = "Showing categories and products for home screen";
        return $this->sendResponse(1,$message,$result);
    }

    public function getAllCategories()
    {
        $result = Category::all();
        $message = "Showing all categories";
        return $this->sendResponse(1, $message, $result);
    }

    public function getAllPopularProducts()
    {
        $result = $this->getTrendingProducts(1, ['paginate' => 20]);
        $message = "Showing all categories";
        return $this->sendResponse(1, $message, $result);
    }

    public function getCategoryProducts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
        }

        $result = $this->getTrendingProducts(1, ['category' => $request->category_id, 'paginate' => 20]);
        $message = "Showing category products";
        return $this->sendResponse(1, $message, $result);
    }

    public function productDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
        }

        $result = Product::active()
            ->select('products.*', 'name as product')
            ->with(['category', 'brand', 'unit', 'variations', 'product_tax'])
            ->where('id', $request->product_id)
            ->first();

        $message = "Showing product details";
        return $this->sendResponse(1, $message, $result);

    }

    public function allProductList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'products' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
        }

        $result = Product::active()
            ->select('products.*', 'name as product')
            ->with(['category', 'brand', 'unit', 'variations', 'product_tax'])
            ->whereIn('id', $request->products)
            ->get();

        $message = "Showing all products list";
        return $this->sendResponse(1, $message, $result);

    }

    public function getBusinessLoc()
    {
        $result = $this->getLocation(1);
        $message = "Showing business location";
        return $this->sendResponse(1, $message, $result);
    }
}