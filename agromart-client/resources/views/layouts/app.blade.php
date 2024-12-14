{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agromart</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom styling */
        .navbar-brand {
            font-weight: bold;
            color: #4CAF50;
        }

        .navbar-brand:hover {
            color: #3E8E41;
        }

        .nav-link {
            transition: color 0.3s ease-in-out;
        }

        .nav-link:hover {
            color: #4CAF50;
        }

        body {
            background-color: #f8f9fa;
        }

        footer {
            background-color: #343a40;
            color: white;
            padding: 15px 0;
        }

        footer a {
            color: #4CAF50;
            text-decoration: none;
            transition: color 0.3s ease-in-out;
        }

        footer a:hover {
            color: #3E8E41;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">Agromart</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('categories.index') }}">Kategori</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('cart.index') }}">Keranjang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('orders.index') }}">Riwayat Pesanan</a>
                    </li>
                    <!-- Button Login -->
                    @guest
                        <li class="nav-item">
                            <a class="btn btn-outline-success" href="{{ route('login') }}">Login</a>
                        </li>
                    @endguest
                    <!-- Jika sudah login, tampilkan tombol Logout -->
                    @auth
                        <li class="nav-item">
                            <a class="btn btn-outline-danger" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>


    <!-- Main content -->
    <main class="container py-5">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="text-center">
        <div class="container">
            <p>&copy; {{ date('Y') }} Agromart. All Rights Reserved.</p>
            <p>
                <a href="#">Kebijakan Privasi</a> | <a href="#">Syarat & Ketentuan</a>
            </p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
