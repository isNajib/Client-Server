<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private const API_URL = 'http://127.0.0.1:8000/api';

    public function index()
    {
        // Mengambil semua item dalam keranjang dari session
        $cartItems = session()->get('cart', []);
        // Menghitung total harga keranjang
        $totalPrice = array_sum(array_column($cartItems, 'subtotal'));

        return view('cart', compact('cartItems', 'totalPrice'));
    }

    public function add($productId, Request $request)
    {
        // Mengambil data produk dari API
        $response = Http::get(self::API_URL . "/products/{$productId}");

        // Mengecek apakah permintaan berhasil
        if (!$response->successful()) {
            return redirect()->back()->with('error', 'Gagal mengambil data produk.');
        }

        $product = $response->json();

        // Validasi jika produk tidak ditemukan dalam response
        if (!isset($product['id'], $product['name'], $product['price'])) {
            return redirect()->back()->with('error', 'Data produk tidak lengkap.');
        }

        // Mengambil data keranjang dari session
        $cart = session()->get('cart', []);
        $quantity = $request->input('quantity', 1);

        // Validasi jumlah quantity
        if ($quantity < 1) {
            return redirect()->back()->with('error', 'Jumlah harus lebih dari 0.');
        }

        // Cek jika produk sudah ada dalam keranjang
        if (isset($cart[$product['id']])) {
            // Tambah jumlah produk yang ada
            $cart[$product['id']]['quantity'] += $quantity;
            // Update subtotal
            $cart[$product['id']]['subtotal'] = $cart[$product['id']]['quantity'] * $product['price'];
        } else {
            // Tambahkan produk baru ke keranjang
            $cart[$product['id']] = [
                'name' => $product['name'],
                'quantity' => $quantity,
                'price' => $product['price'],
                'subtotal' => $product['price'] * $quantity,
            ];
        }

        // Simpan ke session
        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function remove($productId)
    {
        $cart = session()->get('cart', []);
        // Hapus produk dari keranjang
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            // Simpan perubahan ke session
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Produk berhasil dihapus dari keranjang.');
    }

    // Fungsi untuk mengirimkan data keranjang ke OrderController
    public function checkout()
    {
        $cartItems = session()->get('cart', []);
        if (empty($cartItems)) {
            return redirect()->back()->with('error', 'Keranjang Anda kosong.');
        }

        // Menghitung total harga
        $totalPrice = array_sum(array_column($cartItems, 'subtotal'));

        // Mengirim data ke OrderController untuk membuat pesanan baru
        $response = Http::post('http://127.0.0.1:8000/api/orders', [
            'user_id' => 1, // Ganti sesuai dengan ID user yang login
            'order_date' => now()->toDateTimeString(),
            'total_price' => $totalPrice,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        if (!$response->successful()) {
            return redirect()->back()->with('error', 'Gagal membuat pesanan.');
        }

        // Mendapatkan ID pesanan yang baru dibuat
        $orderId = $response->json()['id'];

        // Menyusun data untuk order-items
        $orderItemsData = [];
        foreach ($cartItems as $productId => $item) {
            $orderItemsData[] = [
                'order_id' => $orderId,
                'product_id' => $productId,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['subtotal'],
            ];
        }

        // Mengirim data order-items ke API
        foreach ($orderItemsData as $orderItem) {
            Http::post('http://127.0.0.1:8000/api/order-items', $orderItem);
        }

        // Hapus keranjang setelah pesanan berhasil dibuat
        session()->forget('cart');
        return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dibuat.');
    }
}
