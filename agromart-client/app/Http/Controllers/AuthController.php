<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class AuthController extends Controller
{
    private $baseUrl = 'http://127.0.0.1:8000/api';
    // Tampilkan form login
    public function showLoginForm()
    {
        return view('auth.login'); // pastikan Anda memiliki view login di resources/views/auth
    }

    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Kirim request ke server API
        $response = Http::post("{$this->baseUrl}/register", [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'password_confirmation' => $request->password_confirmation,
        ]);

        // Return response
        return response()->json($response->json(), $response->status());
    }

    // Proses login
    public function login(Request $request)
    {
        $client = new Client();

        try {
            // Kirim request ke server API untuk login
            $response = $client->post("{$this->baseUrl}/login", [
                'form_params' => [
                    'email' => $request->email,
                    'password' => $request->password,
                ],
            ]);

            $responseBody = json_decode($response->getBody(), true);

            // Simpan token di session
            $request->session()->put('token', $responseBody['token']);

            // Ambil data user berdasarkan token dari API
            $userResponse = $client->get("{$this->baseUrl}/user", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $responseBody['token'],
                ],
            ]);

            $user = json_decode($userResponse->getBody(), true);

            // Periksa apakah pengguna adalah admin
            if ($user['email'] === 'admin@gmail.com') {
                return redirect()->route('data.index')->with('success', 'Login berhasil sebagai admin!');
            } else {
                return redirect()->route('categories.index')->with('success', 'Login berhasil!');
            }
        } catch (\Exception $e) {
            // Tangani error jika login gagal
            return redirect()->back()->with('error', 'Login gagal! Periksa kembali email dan password Anda.');
        }
    }
}
