<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function index()
    {
        $client = new Client();
        $url = 'http://127.0.0.1:8000/api/products';
        $response = $client->request('GET', $url);
        $content = $response->getBody()->getContents();
        $contentArray = json_decode($content, true);
        $data = $contentArray;

        // Panggil API untuk me ndapatkan daftar kategori
        $urlCategory = 'http://127.0.0.1:8000/api/categories'; // Gantilah dengan endpoint kategori yang sesuai
        $responseCategory = $client->request('GET', $urlCategory);
        $contentCategory = $responseCategory->getBody()->getContents();
        $categories = json_decode($contentCategory, true);
        // dd($categories);

        // Kirimkan data produk dan kategori ke view
        return view('admin.index', [
            'data' => $data,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'price' => 'required|numeric',
            'image' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048', // Validasi file gambar
            'stock' => 'required|integer',
            'category_id' => 'required', // Validasi ID kategori
        ]);

        // Ambil data dari request
        $name = $request->name;
        $description = $request->description;
        $price = $request->price;
        $image = $request->file('image'); // Mengambil file gambar
        $stock = $request->stock;
        $category = $request->category_id;

        // Inisialisasi parameter data yang akan dikirim ke API
        $parameter = [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'stock' => $stock,
            'category_id' => $category,
        ];

        // Menggunakan Guzzle HTTP Client untuk mengirim request
        $client = new Client();
        $url = 'http://127.0.0.1:8000/api/products';

        // Mengirimkan data ke API dengan gambar menggunakan multipart/form-data
        $response = $client->request('POST', $url, [
            'multipart' => [
                [
                    'name' => 'name',
                    'contents' => $parameter['name'],
                ],
                [
                    'name' => 'description',
                    'contents' => $parameter['description'],
                ],
                [
                    'name' => 'price',
                    'contents' => $parameter['price'],
                ],
                [
                    'name' => 'stock',
                    'contents' => $parameter['stock'],
                ],
                [
                    'name' => 'category_id',
                    'contents' => $parameter['category_id'],
                ],
                [
                    'name' => 'image',
                    'contents' => fopen($image->getRealPath(), 'r'), // File gambar di-upload
                    'filename' => $image->getClientOriginalName(),
                ],
            ],
        ]);

        // Mendapatkan response dari API
        $content = $response->getBody()->getContents();
        $contentArray = json_decode($content, true);

        // Menangani response dari API
        if (isset($contentArray['status']) && $contentArray['status'] != true) {
            $error = $contentArray;
            return redirect()->to('data')->withErrors($error)->withInput();
        } else {
            return redirect()->route('data.index')->with('success', 'Produk berhasil ditambahkan');
        }
    }

    public function edit(string $id)
    {
        $client = new Client();

        // Fetch the product data
        $url = "http://127.0.0.1:8000/api/products/$id";
        $response = $client->request('GET', $url);
        $content = $response->getBody()->getContents();
        $contentArray = json_decode($content, true);
        $data = $contentArray;

        // Fetch the categories
        $urlCategory = 'http://127.0.0.1:8000/api/categories'; // Ensure this endpoint exists
        $responseCategory = $client->request('GET', $urlCategory);
        $contentCategory = $responseCategory->getBody()->getContents();
        $categories = json_decode($contentCategory, true);

        if (isset($contentArray['status']) && $contentArray['status'] != true) {
            $error = $contentArray['message'];
            return redirect()->to('data')->withErrors($error);
        } else {
            return view('admin.index', ['data' => $data, 'categories' => $categories]);
        }
    }

    public function update(Request $request, string $id)
    {
        // Validasi input (tanpa gambar)
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_id' => 'required', // Validasi ID kategori
        ]);

        // Ambil data dari request
        $name = $request->name;
        $description = $request->description;
        $price = $request->price;
        $stock = $request->stock;
        $category = $request->category_id;

        // Menggunakan Guzzle HTTP Client untuk mengirim request
        $client = new Client();
        $url = "http://127.0.0.1:8000/api/products/{$id}"; // Perbaiki URL

        // Kirimkan data tanpa gambar jika tidak ada perubahan gambar
        $response = $client->request('PUT', $url, [
            'form_params' => [
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'stock' => $stock,
                'category_id' => $category,
            ],
        ]);

        // Mendapatkan response dari API
        $content = $response->getBody()->getContents();
        $contentArray = json_decode($content, true);

        // Menangani response dari API
        if (isset($contentArray['status']) && $contentArray['status'] != true) {
            $error = $contentArray;
            return redirect()->to('data')->withErrors($error)->withInput();
        } else {
            return redirect()->route('data.index')->with('success', 'Produk berhasil diupdate');
        }
    }

    public function destroy(string $id)
    {
        $client = new Client();
        $url = "http://127.0.0.1:8000/api/products/{$id}";
        $response = $client->request('DELETE', $url);
        $content = $response->getBody()->getContents();
        $contentArray = json_decode($content, true);
        if (isset($contentArray['status']) && $contentArray['status'] != true) {
            $error = $contentArray;
            return redirect()->to('data')->withErrors($error)->withInput();
        } else {
            return redirect()->route('data.index')->with('success', 'Produk berhasil dihapus');
        }
    }
}
