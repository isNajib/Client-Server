@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Kategori Produk</h2>
        <div class="row">
            @foreach ($categories as $category)
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h3>{{ $category['name'] }}</h3> <!-- Akses dengan array -->
                            <p>{{ $category['description'] ?? 'No description available.' }}</p> <!-- Akses dengan array -->
                            <a href="{{ route('products.index', ['categoryId' => $category['id']]) }}"
                                class="btn btn-primary">Lihat Produk</a> <!-- Akses dengan array -->
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
