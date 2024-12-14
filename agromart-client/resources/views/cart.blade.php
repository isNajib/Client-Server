{{-- cart.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Keranjang Belanja</h2>

        {{-- Menampilkan pesan jika keranjang kosong --}}
        @if (session()->get('cart', []) == [])
            <div class="alert alert-warning">
                Keranjang Anda kosong.
            </div>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Looping setiap item dalam keranjang --}}
                    @foreach ($cartItems as $productId => $item)
                        <tr>
                            <td>{{ $item['name'] }}</td>
                            <td>{{ number_format($item['price'], 2) }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>{{ number_format($item['subtotal'], 2) }}</td>
                            <td>
                                {{-- Hapus item dari keranjang --}}
                                <form action="{{ route('cart.remove', $productId) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Menampilkan total harga --}}
            <div class="text-right">
                <h4>Total Harga: {{ number_format($totalPrice, 2) }}</h4>
            </div>

            {{-- Tombol checkout --}}
            <form action="{{ route('cart.checkout') }}" method="POST">
                @csrf
                <button type="submit" id="checkout-button" class="btn btn-success">Checkout</button>
            </form>
        @endif
    </div>
@endsection
