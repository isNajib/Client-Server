@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Header -->
        <h2>{{ $product['name'] }}</h2> <!-- Mengakses 'name' dalam array -->

        <!-- Bagian gambar dan detail produk -->
        <div class="row align-items-center">
            <!-- Kolom Gambar -->
            <div class="col-md-4">
                <img src="{{ asset('storage/' . $product['image']) }}" class="img-fluid" alt="{{ $product['name'] }}">
            </div>

            <!-- Kolom Detail -->
            <div class="col-md-8">
                <p>{{ $product['description'] ?? 'No description available.' }}</p>
                <!-- Mengakses 'description' dalam array -->
                <p><strong>Harga:</strong> Rp {{ number_format($product['price'], 0, ',', '.') }}</p>
                <!-- Mengakses 'price' dalam array -->

                <!-- Form untuk menambahkan produk ke keranjang -->
                <form action="{{ route('cart.add', $product['id']) }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="quantity">Jumlah:</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" value="1"
                            min="1" max="{{ $product['stock'] }}"> <!-- Mengakses 'stock' dalam array -->
                    </div>
                    <button type="submit" class="btn btn-success">Tambahkan ke Keranjang</button>
                </form>
            </div>
        </div>

        <hr>

        <!-- Form untuk menambahkan ulasan -->
        <h3>Tambah Ulasan</h3>
        <form action="{{ url('reviews') }}" method="POST">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product['id'] }}">

            <div class="form-group">
                <label for="rating">Rating:</label>
                <select name="rating" id="rating" class="form-control" required>
                    <option value="">Pilih Rating</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>

            <div class="form-group mt-2">
                <label for="review">Ulasan:</label>
                <textarea name="review" id="review" class="form-control" rows="4"></textarea>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Kirim Ulasan</button>
        </form>

        <hr>

        <!-- Menampilkan ulasan produk -->
        <h3>Ulasan Pengguna</h3>
        <div id="reviews">
            @foreach ($product['reviews'] as $review)
                <div class="review">
                    <!-- Menampilkan nama user, jika ada -->
                    <strong>{{ $review['user']['name'] ?? 'Unknown User' }}</strong> - Rating: {{ $review['rating'] }}<br>
                    <p>{{ $review['review'] }}</p> <!-- Menampilkan ulasan -->
                </div>
            @endforeach
        </div>
    </div>
@endsection
