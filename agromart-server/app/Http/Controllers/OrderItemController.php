<?php
namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrderItemController extends Controller
{
    // Menampilkan semua order items
    public function index()
    {
        return response()->json(OrderItem::all(), 200);
    }

    // Menyimpan order item baru
    public function store(Request $request)
    {
        // Validasi input yang diterima
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id', // Pastikan order_id ada di tabel orders
            'product_id' => 'required|exists:products,id', // Pastikan product_id ada di tabel products
            'quantity' => 'required|integer|min:1', // Pastikan quantity lebih dari 0
            'price' => 'required|numeric|min:0', // Pastikan harga valid
            'subtotal' => 'required|numeric|min:0', // Pastikan subtotal valid
        ]);

        // Jika validasi gagal, kembalikan pesan kesalahan
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Memulai transaksi untuk memastikan konsistensi data
        DB::beginTransaction();

        try {
            // Menyimpan data order item ke dalam database
            $orderItem = OrderItem::create([
                'order_id' => $request->order_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'subtotal' => $request->subtotal,
            ]);

            // Mengurangi stok produk terkait jika stok cukup
            $product = Product::find($request->product_id);

            // Cek apakah stok cukup untuk produk yang dipesan
            if ($product->stock < $request->quantity) {
                // Jika stok tidak cukup, rollback transaksi dan beri pesan kesalahan
                DB::rollback();
                return response()->json(['message' => 'Stok produk tidak cukup'], 400);
            }

            // Kurangi stok produk sesuai dengan quantity yang dipesan
            $product->decrement('stock', $request->quantity);

            // Commit transaksi jika tidak ada error
            DB::commit();

            // Mengembalikan response JSON dengan data order item yang berhasil disimpan
            return response()->json($orderItem, 201);
        } catch (\Exception $e) {
            // Rollback transaksi jika ada error
            DB::rollback();
            return response()->json(['message' => 'Gagal menambahkan order item: ' . $e->getMessage()], 500);
        }
    }

    // Menampilkan order item berdasarkan ID
    public function show($id)
    {
        $orderItem = OrderItem::find($id);
        if (!$orderItem) {
            return response()->json(['message' => 'Order Item not found'], 404);
        }

        return response()->json($orderItem, 200);
    }

    // Mengupdate order item
    public function update(Request $request, $id)
    {
        $orderItem = OrderItem::find($id);
        if (!$orderItem) {
            return response()->json(['message' => 'Order Item not found'], 404);
        }

        $orderItem->update($request->all());
        return response()->json($orderItem, 200);
    }

    // Menghapus order item
    public function destroy($id)
    {
        $orderItem = OrderItem::find($id);
        if (!$orderItem) {
            return response()->json(['message' => 'Order Item not found'], 404);
        }

        $orderItem->delete();
        return response()->json(['message' => 'Order Item deleted successfully'], 200);
    }
}
