<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Order::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userId = auth()->id();

        $order = Order::create([
            "user_id" => $userId
        ]);
        $resOrder = [
            "id" => $order->id,
            "created_at" => $order->created_at,
            "updated_at" => $order->updated_at,
            "user_id" => $order->user_id
        ];
        foreach ($request->items as $item) {
            $realItem = Item::find($item['item_id']);
            $resOrder['items'][] = OrderItem::create([
                "order_id" => $order->id,
                'count' => $item['count'],
                "item_id" => $item['item_id'],
                "actual_price" => $realItem->price
            ]);
        }
        return response()->json($resOrder, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        $resOrder = [
            "id" => $order->id,
            "created_at" => $order->created_at,
            "updated_at" => $order->updated_at,
            "user_id" => $order->user_id
        ];
        foreach ($order->orderItems as $item) {
            $resOrder['items'][] = $item;
        }
        return response()->json($resOrder, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        try {
            $order->delete();
            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }
}
