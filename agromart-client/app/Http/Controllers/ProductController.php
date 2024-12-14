<?php

// app/Http/Controllers/ProductController.php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index($categoryId)
    {
        // Mengambil data produk dari API berdasarkan kategori
        $response = Http::get('http://127.0.0.1:8000/api/products', [
            'category_id' => $categoryId, // Menggunakan parameter kategori langsung
        ]);

        if ($response->successful()) {
            $products = $response->json(); // Mendapatkan data produk dalam format array
            return view('products', compact('categoryId', 'products')); // Mengirimkan kategori id dan produk
        }

        return redirect()->back()->with('error', 'Gagal mengambil data produk.');
    }

    public function show($id)
    {
        // Mengambil data produk dari Agromart API (server agromart)
        $productResponse = Http::get("http://127.0.0.1:8000/api/products/{$id}");

        if ($productResponse->successful()) {
            $product = $productResponse->json();

            // Mengambil ulasan produk dari server review
            $reviewsResponse = Http::get('http://127.0.0.1:8002/api/reviews', [
                'product_id' => $id, // Parameter untuk mengambil ulasan produk
            ]);

            // Cek apakah request ulasan berhasil
            $product['reviews'] = $reviewsResponse->successful() ? $reviewsResponse->json() : [];

            return view('product-show', compact('product'));
        }

        return redirect()->back()->with('error', 'Gagal mengambil data produk.');
    }

    public function storeReview(Request $request)
    {
        // Mengirimkan data ulasan ke server review
        $response = Http::post('http://127.0.0.1:8002/api/reviews', [
            'user_id' => 1, // ID user yang sedang login
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        if ($response->successful()) {
            return redirect()
                ->route('product.show', $request->product_id)
                ->with('success', 'Ulasan berhasil ditambahkan.');
        }

        return back()->with('error', 'Gagal menambahkan ulasan.');
    }
}
