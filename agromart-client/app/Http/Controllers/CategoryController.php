<?php

// app/Http/Controllers/CategoryController.php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class CategoryController extends Controller
{
    public function index()
    {
        // Mengambil data kategori dari API
        $response = Http::get('http://127.0.0.1:8000/api/categories');

        if ($response->successful()) {
            $categories = $response->json();
            return view('categories', compact('categories'));
        }

        return redirect()->back()->with('error', 'Gagal mengambil data kategori.');
    }
}
