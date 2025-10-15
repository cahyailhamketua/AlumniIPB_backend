<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::all();
        return response()->json($articles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal' => 'required|date',
            'like' => 'integer|min:0',
            'komentar' => 'integer|min:0',
            'kategori' => 'required|string|max:255',
            'isi_artikel' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('article_images', 'public');
            $validatedData['image'] = $imagePath;
        }

        $article = Article::create($validatedData);

        return response()->json($article, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        return response()->json($article);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        $validatedData = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal' => 'required|date',
            'like' => 'integer|min:0',
            'komentar' => 'integer|min:0',
            'kategori' => 'required|string|max:255',
            'isi_artikel' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }
            $imagePath = $request->file('image')->store('article_images', 'public');
            $validatedData['image'] = $imagePath;
        }

        $article->update($validatedData);

        return response()->json($article);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        // Delete image from storage
        if ($article->image) {
            Storage::disk('public')->delete($article->image);
        }

        $article->delete();

        return response()->json(['message' => 'Article deleted successfully']);
    }

    public function like(string $id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        $article->increment('like');
        return response()->json(['message' => 'Article liked', 'likes' => $article->like]);
    }

    public function comment(Request $request, string $id)
    {
        // For now, I'm just incrementing the comment count.
        // In a real application, you would store the actual comment in a separate table
        // and associate it with the article and the authenticated user.

        $article = Article::find($id);

        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:255',
        ]);

        $article->increment('komentar');
        // Here you would typically save the comment to a 'comments' table.
        // For demonstration, I'm just returning a success message.
        return response()->json(['message' => 'Comment added', 'comments' => $article->komentar]);
    }
}
