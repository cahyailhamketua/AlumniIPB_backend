<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Alumni; // Import Alumni model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AlumniController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $alumni = Alumni::with('user')->get(); // Use get() if you don't want pagination metadata
        // If you still want pagination, use: $alumni = Alumni::with('user')->paginate(10);
        return response()->json($alumni, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nomor_telepon' => 'required|string|max:20',
            'fakultas' => 'required|string|max:255',
            'angkatan' => 'required|string|max:10',
            'password' => 'required|string|min:6|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'pekerjaan' => 'nullable|string|max:255',
            'perusahaan' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'biografi' => 'nullable|string',
            'riwayat_pekerjaan' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('alumni_images', 'public');
        }

        // Create the User first
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'alumni',
        ]);

        // Then create the associated Alumni record
        $alumni = Alumni::create([
            'user_id' => $user->id,
            'nama' => $request->nama,
            'nomor_telepon' => $request->nomor_telepon,
            'fakultas' => $request->fakultas,
            'angkatan' => $request->angkatan,
            'image' => $imagePath,
            'pekerjaan' => $request->pekerjaan ?? null,
            'perusahaan' => $request->perusahaan ?? null,
            'alamat' => $request->alamat ?? null,
            'biografi' => $request->biografi ?? null,
            'riwayat_pekerjaan' => $request->riwayat_pekerjaan ?? null,
        ]);

        return response()->json(['message' => 'Pendaftaran berhasil', 'user' => $user, 'alumni_profile' => $alumni], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $alumni = Alumni::with('user')->find($id);

        if (!$alumni) {
            return response()->json(['message' => 'Alumni not found'], 404);
        }

        return response()->json($alumni, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $alumni = Alumni::with('user')->findOrFail($id);

        // Cek apakah user yang login adalah pemilik akun ini
        if (Auth::id() !== $alumni->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu tidak memiliki izin untuk mengedit data ini.'
            ], 403);
        }

        // Validasi input
        $validated = $request->validate([
            'nama' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $alumni->user_id,
            'nomor_telepon' => 'nullable|string|max:20',
            'fakultas' => 'nullable|string|max:100',
            'angkatan' => 'nullable|string|max:10',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'pekerjaan' => 'nullable|string|max:255',
            'perusahaan' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'biografi' => 'nullable|string',
            'riwayat_pekerjaan' => 'nullable|array',
        ]);

        $imagePath = $alumni->image;
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($alumni->image) {
                Storage::disk('public')->delete($alumni->image);
            }
            $imagePath = $request->file('image')->store('alumni_images', 'public');
        }

        // Update data di tabel users (email)
        if (isset($validated['email'])) {
            $alumni->user->update([
                'email' => $validated['email'] ?? $alumni->user->email,
            ]);
        }

        // Update data di tabel alumni (nama, nomor_telepon, fakultas, angkatan)
        $alumni->update([
            'nama' => $validated['nama'] ?? $alumni->nama,
            'nomor_telepon' => $validated['nomor_telepon'] ?? $alumni->nomor_telepon,
            'fakultas' => $validated['fakultas'] ?? $alumni->fakultas,
            'angkatan' => $validated['angkatan'] ?? $alumni->angkatan,
            'image' => $imagePath,
            'pekerjaan' => $validated['pekerjaan'] ?? $alumni->pekerjaan,
            'perusahaan' => $validated['perusahaan'] ?? $alumni->perusahaan,
            'alamat' => $validated['alamat'] ?? $alumni->alamat,
            'biografi' => $validated['biografi'] ?? $alumni->biografi,
            'riwayat_pekerjaan' => $validated['riwayat_pekerjaan'] ?? $alumni->riwayat_pekerjaan,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profil alumni berhasil diperbarui.',
            'data' => $alumni->load('user:id,email'),
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $alumni = Alumni::find($id);

        if (!$alumni) {
            return response()->json(['message' => 'Alumni not found'], 404);
        }

        $user = $alumni->user;

        if ($alumni->delete()) {
            // Delete associated image if exists
            if ($alumni->image) {
                Storage::disk('public')->delete($alumni->image);
            }
            // Also delete the associated User record if it exists
            if ($user) {
                $user->delete();
            }
            return response()->json(['message' => 'Alumni and associated user deleted successfully'], 200);
        }

        return response()->json(['message' => 'Failed to delete alumni'], 500);
    }

    /**
     * Login an alumni.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find the user in the 'users' table and eager load alumni data
        $user = User::where('email', $request->email)->with('alumni')->first();

        // Check if user exists and password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau password salah'], 401);
        }

        // Generate Sanctum token for the user
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => $user->load('alumni'), // Ensure alumni data is loaded
            'role' => $user->role // Include user role in the response
        ]);
    }

     /**
     * Logout an alumni.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil'], 200);
    }

    /**
     * Search for alumni by nama or fakultas.
     */
    public function search(Request $request)
    {
        $query = $request->input('query');

        $alumni = Alumni::with('user') 
            ->where('nama', 'like', '%' . $query . '%')
            ->orWhere('fakultas', 'like', '%' . $query . '%')
            ->get();

        return response()->json($alumni, 200);
    }
}
