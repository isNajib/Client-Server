<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
</head>

<body class="bg-light">
    <main class="container">
        <!-- START FORM -->
        <div class="my-3 p-3 bg-body rounded shadow-sm">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session()->has('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <form action="{{ isset($data['id']) ? route('data.update', $data['id']) : route('products.store') }}"
                method="POST" enctype="multipart/form-data">
                @csrf

                {{-- @if (Route::current()->uri == 'data/{id}') --}}
                @if (isset($data['id']))
                    @method('PUT')
                @endif
                {{-- @endif --}}

                <div class="mb-3 row">
                    <label for="name" class="col-sm-2 col-form-label">Nama Barang</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="name" id="name"
                            value="{{ isset($data['name']) ? $data['name'] : old('name') }}" required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="description" class="col-sm-2 col-form-label">Deskripsi</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="description" id="description"
                            value="{{ isset($data['description']) ? $data['description'] : old('description') }}"
                            required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="price" class="col-sm-2 col-form-label">Harga</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="price" id="price"
                            value="{{ isset($data['price']) ? $data['price'] : old('price') }}" required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="image" class="col-sm-2 col-form-label">Gambar</label>
                    <div class="col-sm-10">
                        <input type="file" class="form-control" name="image" id="image"
                            value="{{ isset($data['image']) ? $data['image'] : old('image') }}" required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="stock" class="col-sm-2 col-form-label">Stok</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="stock" id="stock"
                            value="{{ isset($data['stock']) ? $data['stock'] : old('stock') }}" required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="category_id" class="col-sm-2 col-form-label">Kategori</label>
                    <div class="col-sm-10">
                        <select name="category_id" id="category_id" class="form-control" required>
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $item)
                                <option value="{{ $item['id'] }}"
                                    {{ isset($data['category_id']) && $data['category_id'] == $item['id'] ? 'selected' : '' }}>
                                    {{ $item['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-2 col-form-label"></label>
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary">SIMPAN</button>
                    </div>
                </div>
            </form>
        </div>
        <!-- AKHIR FORM -->

        @if (Route::current()->uri == 'data')

            <!-- START DATA -->
            <div class="my-3 p-3 bg-body rounded shadow-sm">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="col-md-1">No</th>
                            <th class="col-md-2">Nama Barang</th>
                            <th class="col-md-3">Deskripsi</th>
                            <th class="col-md-1">Harga</th>
                            <th class="col-md-2">Gambar</th>
                            <th class="col-md-1">Stok</th>
                            <th class="col-md-1">Kategori ID</th>
                            <th class="col-md-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['description'] }}</td>
                                <td>{{ $item['price'] }}</td>
                                <td>{{ $item['image'] }}</td>
                                <td>{{ $item['stock'] }}</td>
                                <td>{{ $item['category_id'] }}</td>
                                <td>
                                    <a href="{{ route('data.edit', $item['id']) }}"
                                        class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('data.delete', $item['id']) }}" method="post"
                                        onsubmit="return confirm('Apakah Anda yakin akan melakukan penghapusan data?')"
                                        class="d-inline">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" name="submit" class="btn btn-danger btn-sm">Del</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- AKHIR DATA -->
        @endif
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous">
    </script>
</body>

</html>
