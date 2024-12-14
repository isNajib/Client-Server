<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    // Menampilkan semua order
    public function index()
    {
        // Menampilkan semua order beserta relasi terkait jika diperlukan
        return response()->json(Order::with('customer', 'orderItems')->get(), 200);
    }

    // Menyimpan order baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'order_date' => 'required|date', // Pastikan order_date ada dan dalam format tanggal
            'total_price' => 'required|numeric|min:0', // Menggunakan total_price sebagai ganti total
            'status' => 'required|string|in:pending,completed,canceled',
            'payment_status' => 'required|string|in:unpaid,paid', // Menggunakan payment_status
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $order = Order::create([
            'user_id' => $request->user_id,
            'order_date' => $request->order_date,
            'total_price' => $request->total_price,
            'status' => $request->status,
            'payment_status' => $request->payment_status,
        ]);

        return response()->json($order, 201);
    }

    // Menampilkan order berdasarkan ID
    public function show($id)
    {
        $order = Order::with('customer', 'orderItems')->find($id); // Menambahkan relasi
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json($order, 200);
    }

    // Mengupdate order
    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'order_date' => 'sometimes|required|date',
            'total_price' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|string|in:pending,completed,canceled',
            'payment_status' => 'sometimes|required|string|in:unpaid,paid',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $order->update($request->all());
        return response()->json($order, 200);
    }

    // Menghapus order
    public function destroy($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->delete();
        return response()->json(['message' => 'Order deleted successfully'], 200);
    }
}
