<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Menampilkan daftar pesanan
    public function index()
    {
        // Mengambil riwayat pesanan dari API
        $response = Http::get('http://127.0.0.1:8000/api/orders');

        if ($response->successful()) {
            $orders = $response->json();
            return view('orders', compact('orders'));
        }

        return redirect()->back()->with('error', 'Gagal mengambil data pesanan.');
    }

    // Membuat pesanan baru
    public function create()
    {
        // Mengambil data keranjang belanja
        $cartItems = session()->get('cart', []);

        if (empty($cartItems)) {
            return redirect()->back()->with('error', 'Keranjang Anda kosong.');
        }

        // Menghitung total harga
        $totalPrice = array_sum(array_column($cartItems, 'subtotal'));

        // Mendapatkan user_id dari pengguna yang sedang login
        $userId = Auth::id(); // Mengambil user_id yang sedang login

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        // Menggunakan Guzzle Client untuk mengirimkan request
        $client = new Client();

        try {
            // Mengirim data pesanan ke API menggunakan Guzzle
            $response = $client->request('POST', 'http://127.0.0.1:8000/api/orders', [
                'json' => [
                    'user_id' => $userId, // Menggunakan user_id yang didapatkan
                    'order_date' => now()->toDateTimeString(),
                    'total_price' => $totalPrice,
                    'status' => 'pending',
                    'payment_status' => 'unpaid',
                ],
            ]);

            // Memastikan request sukses dan mendekode respons
            $content = $response->getBody()->getContents();
            $contentArray = json_decode($content, true);
            if (isset($contentArray['id'])) {
                $orderId = $contentArray['id'];

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

                // Mengirim data order-items ke API menggunakan Guzzle
                foreach ($orderItemsData as $orderItem) {
                    $itemResponse = $client->request('POST', 'http://127.0.0.1:8000/api/order-items', [
                        'json' => $orderItem,
                    ]);

                    // Cek apakah pengiriman item sukses
                    if ($itemResponse->getStatusCode() !== 201) {
                        throw new \Exception('Gagal menyimpan item pesanan.');
                    }
                }

                // Hapus keranjang setelah pesanan berhasil dibuat
                session()->forget('cart');
                return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dibuat.');
            } else {
                throw new \Exception('Gagal membuat pesanan.');
            }
        } catch (RequestException $e) {
            // Tangani kesalahan jaringan atau API tidak dapat dihubungi
            return redirect()
                ->back()
                ->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Tangani kesalahan umum lainnya
            return redirect()
                ->back()
                ->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }
}
