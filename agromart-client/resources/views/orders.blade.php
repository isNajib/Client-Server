@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Riwayat Pesanan</h2>

        @if (!empty($orders))
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Tanggal Pesanan</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>{{ $order['id'] }}</td> <!-- Akses dengan array -->
                            <td>{{ $order['order_date'] }}</td> <!-- Akses dengan array -->
                            <td>Rp {{ number_format($order['total_price'], 0, ',', '.') }}</td> <!-- Akses dengan array -->
                            <td>{{ $order['status'] }}</td> <!-- Akses dengan array -->
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>Tidak ada pesanan.</p>
        @endif
    </div>
@endsection
