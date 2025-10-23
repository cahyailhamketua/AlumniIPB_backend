<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gallery;
use App\Models\GalleryComment;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $galleries = Gallery::select(
            'id',
            'judul_galery',
            'deskripsi',
            'tanggal',
            'kategori',
            'jumlah_peserta',
            'foto_kegiatan',
            'lokasi'
        )
        ->withCount(['usersWhoLiked as likes_count'])
            ->orderBy('tanggal', 'desc');

        if ($request->has('sortByCategory')) {
            $galleries->orderBy('kategori');
        }

        if ($request->has('sortByYear')) {
            $galleries->orderByRaw('YEAR(tanggal)');
        }

        return response()->json($galleries->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'judul_galery' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal' => 'required|date',
            'kategori' => 'required|string|max:255',
            'jumlah_peserta' => 'integer|min:0',
            'foto_kegiatan' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'lokasi' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('foto_kegiatan')) {
            $imagePath = $request->file('foto_kegiatan')->store('gallery_photos', 'public');
            $validatedData['foto_kegiatan'] = $imagePath;
        }

        $gallery = Gallery::create($validatedData);

        return response()->json($gallery, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $gallery = Gallery::with(['usersWhoLiked', 'comments'])->find($id);

        if (!$gallery) {
            return response()->json(['message' => 'Gallery not found'], 404);
        }

        return response()->json($gallery);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd($request->all()); // Temporarily added for debugging
        \Log::info('Update Gallery Request Data:', $request->all()); // Log request data
        $gallery = Gallery::find($id);

        if (!$gallery) {
            return response()->json(['message' => 'Gallery not found'], 404);
        }

        $validatedData = $request->validate([
            'judul_galery' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal' => 'required|date',
            'kategori' => 'required|string|max:255',
            'jumlah_peserta' => 'integer|min:0',
            'foto_kegiatan' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'lokasi' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('foto_kegiatan')) {
            // Delete old image if exists
            if ($gallery->foto_kegiatan) {
                Storage::disk('public')->delete($gallery->foto_kegiatan);
            }
            $imagePath = $request->file('foto_kegiatan')->store('gallery_photos', 'public');
            $validatedData['foto_kegiatan'] = $imagePath;
        }

        $gallery->update($validatedData);

        return response()->json($gallery);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $gallery = Gallery::find($id);

        if (!$gallery) {
            return response()->json(['message' => 'Gallery not found'], 404);
        }

        // Delete image from storage
        if ($gallery->foto_kegiatan) {
            Storage::disk('public')->delete($gallery->foto_kegiatan);
        }

        $gallery->delete();

        return response()->json(['message' => 'Gallery deleted successfully']);
    }

    public function like(string $id)
    {
        $gallery = Gallery::find($id);

        if (!$gallery) {
            return response()->json(['message' => 'Gallery not found'], 404);
        }

        $user = auth()->user();

        if ($user->likedGalleries()->where('gallery_id', $gallery->id)->exists()) {
            // Unlike the gallery
            $user->likedGalleries()->detach($gallery->id);
            $message = 'Gallery unliked';
        } else {
            // Like the gallery
            $user->likedGalleries()->attach($gallery->id);
            $message = 'Gallery liked';
        }

        $likesCount = $gallery->usersWhoLiked()->count();

        return response()->json(['message' => $message, 'likes' => $likesCount]);
    }

    public function comment(Request $request, string $id)
    {
        $gallery = Gallery::find($id);

        if (!$gallery) {
            return response()->json(['message' => 'Gallery not found'], 404);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:255',
        ]);

        $comment = GalleryComment::create([
            'user_id' => auth()->id(),
            'gallery_id' => $gallery->id,
            'content' => $validated['content'],
        ]);

        return response()->json(['message' => 'Comment added', 'comment' => $comment], 201);
    }

    public function getGalleriesByCategory(string $kategori)
    {
        $galleries = Gallery::select(
            'id',
            'judul_galery',
            'deskripsi',
            'tanggal',
            'kategori',
            'jumlah_peserta',
            'foto_kegiatan',
            'lokasi'
        )
        ->withCount(['usersWhoLiked as likes_count'])
            ->where('kategori', $kategori)
            ->orderBy('tanggal', 'desc')
            ->get();
        return response()->json($galleries);
    }

    public function getGalleriesByYear(string $year)
    {
        $galleries = Gallery::select(
            'id',
            'judul_galery',
            'deskripsi',
            'tanggal',
            'kategori',
            'jumlah_peserta',
            'foto_kegiatan',
            'lokasi'
        )
        ->withCount(['usersWhoLiked as likes_count'])
                            ->whereYear('tanggal', $year)
                            ->orderBy('tanggal', 'desc')
                            ->get();
        return response()->json($galleries);
    }

    public function getAllCategories()
    {
        $categories = Gallery::select('kategori')->distinct()->pluck('kategori');
        return response()->json($categories);
    }

    public function getAllYears()
    {
        $years = Gallery::selectRaw('YEAR(tanggal) as year')
                        ->distinct()
                        ->orderBy('year', 'desc')
                        ->pluck('year');
        return response()->json($years);
    }
}
