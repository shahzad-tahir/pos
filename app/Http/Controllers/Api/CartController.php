<?php


namespace App\Http\Controllers\Api;


use App\Contact;
use App\Product;
use App\UserOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends ResponseController
{
    use Common;

    public function getCartProducts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'products' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
        }

        $products = $request->products;

        $result = $this->getTrendingProducts(1, ['products' => $products]);

        $message = "Showing all products";
        return $this->sendResponse(1, $message, $result);
    }

    public function saveOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:contacts,id',
            'user_name' => 'required|string',
            'mobile' => 'required|string|max:20',
            'address' => 'required|string',
            'products' => 'required|array|min:1',
            'quantity' => 'required|array|min:1',
            'latitude' => 'required',
            'longitude' => 'required',
            'total_amount' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
        }

        $order = UserOrder::create([
            'contact_id' => $request->user_id,
            'user_name' => $request->user_name,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => 'pending',
            'total_amount' => $request->total_amount
        ]);

        $products = $request->products;
        $quantity = $request->quantity;

        for ($i = 0, $iMax = count($products); $i < $iMax; $i++) {
            $order->orderDetails()->create([
               'product_id' => $products[$i],
               'quantity' => $quantity[$i]
            ]);
        }

        //update contact
        $contact = Contact::find($request->user_id);
        $contact->update([
            'name' => $request->user_name,
            'mobile' => $request->mobile,
            'landmark' => $request->address,
            'custom_field3' => $request->latitude,
            'custom_field4' => $request->longitude
        ]);

        $result = UserOrder::with('orderDetails')->find($order->id);
        $productIds = $result->orderDetails->pluck('product_id');
        $result['order_products'] = $this->getTrendingProducts(1, ['products' => $productIds]);

        $message = 'Order created successfully';
        return $this->sendResponse(1,$message,$result);
    }
}