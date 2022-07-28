<?php


namespace App\Http\Controllers\Api;


use App\UserOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends ResponseController
{
    use Common;

    public function getCompletedOrders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:contacts,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
        }

        if ($request->has('user_id') && !empty($request->user_id)) {
            $orders = UserOrder::with('orderDetails')
                ->userOrders($request->user_id)
                ->status('completed')
                ->latest()
                ->paginate('20');

            $message = 'Showing all completed orders of user';
            return $this->sendResponse(1,$message,$orders);
        }

        $orders = UserOrder::with('orderDetails')
            ->status('completed')
            ->latest()
            ->paginate('20');

        $message = 'Showing all completed orders';
        return $this->sendResponse(1,$message,$orders);
    }

    public function getPendingOrders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:contacts,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
        }

        if ($request->has('user_id') && !empty($request->user_id)) {
            $orders = UserOrder::with('orderDetails')
                ->userOrders($request->user_id)
                ->status('pending')
                ->latest()
                ->paginate('20');

            $message = 'Showing all pending orders of user';
            return $this->sendResponse(1,$message,$orders);
        }

        $orders = UserOrder::with('orderDetails')
            ->status('pending')
            ->latest()
            ->paginate('20');

        $message = 'Showing all pending orders';
        return $this->sendResponse(1,$message,$orders);
    }

    public function editOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'nullable|exists:user_orders,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
        }

        $order = UserOrder::with('orderDetails')->find($request->order_id);

        $message = 'Showing order details';
        return $this->sendResponse(1,$message,$order);
    }

    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:user_orders,id',
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

        $order = UserOrder::find($request->order_id);

        $order->update([
            'contact_id' => $request->user_id,
            'user_name' => $request->user_name,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'update_reason' => $request->reason,
            'status' => $request->status ?: 'pending',
            'total_amount' => $request->total_amount
        ]);


        if(count($request->products) > 0) {
            $order->orderDetails()->delete();

            $products = $request->products;
            $quantity = $request->quantity;

            for ($i = 0, $iMax = count($products); $i < $iMax; $i++) {
                $order->orderDetails()->create([
                    'product_id' => $products[$i],
                    'quantity' => $quantity[$i]
                ]);
            }
        }

        $result = UserOrder::with('orderDetails')->find($order->id);
        $productIds = $result->orderDetails->pluck('product_id');
        $result['order_products'] = $this->getTrendingProducts(1, ['products' => $productIds]);

        $message = 'Order updated successfully';
        return $this->sendResponse(1,$message,$result);
    }

    public function changeStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'nullable|exists:user_orders,id',
            'status' => 'required|in:pending,completed'
        ]);

        if ($validator->fails()) {
            return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
        }

        $order = UserOrder::find($request->order_id);

        $order->status = $request->status;
        $order->save();

        $message = 'Order status changed successfully';
        return $this->sendResponse(1,$message,null);
    }
}