@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <h2 class="mb-4">Produk Kategori: {{ $categoryId }}</h2> <!-- Menampilkan ID kategori -->
        <div class="row g-4">
            @foreach ($products as $product)
                <div class="col-md-4 col-lg-3">
                    <div class="card shadow-sm h-100">
                        <!-- Gambar produk jika ada -->
                        @if (isset($product['image']))
                            <img src="{{ $product['image'] }}" class="card-img-top" alt="{{ $product['name'] }}">
                        @else
                            <img src="https://via.placeholder.com/150" class="card-img-top" alt="No image">
                        @endif
                        <div class="card-body d-flex flex-column" style="padding-bottom: 1.5rem;">
                            <!-- Menambahkan padding bawah -->
                            <h5 class="card-title">{{ $product['name'] }}</h5> <!-- Nama produk -->
                            <p class="card-text text-muted small">
                                {{ $product['description'] ?? 'Deskripsi tidak tersedia.' }}
                            </p>
                            <div class="mt-auto">
                                <a href="{{ route('product.show', $product['id']) }}"
                                    class="btn btn-primary btn-sm w-100">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
