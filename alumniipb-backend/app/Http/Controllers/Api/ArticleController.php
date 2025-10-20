<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Comment; // Add this import
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::with(['usersWhoLiked', 'comments'])->get();
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
        $article = Article::with(['usersWhoLiked', 'comments'])->find($id);

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

        $user = auth()->user();

        if ($user->likedArticles()->where('article_id', $article->id)->exists()) {
            // Unlike the article
            $user->likedArticles()->detach($article->id);
            $message = 'Article unliked';
        } else {
            // Like the article
            $user->likedArticles()->attach($article->id);
            $message = 'Article liked';
        }

        $likesCount = $article->usersWhoLiked()->count();

        return response()->json(['message' => $message, 'likes' => $likesCount]);
    }

    public function comment(Request $request, string $id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:255',
        ]);

        $comment = Comment::create([
            'user_id' => auth()->id(),
            'article_id' => $article->id,
            'content' => $validated['content'],
        ]);

        return response()->json(['message' => 'Comment added', 'comment' => $comment], 201);
    }

    public function getArticlesByCategory(string $kategori)
    {
        $articles = Article::with(['usersWhoLiked', 'comments'])->where('kategori', $kategori)->get();
        return response()->json($articles);
    }

    public function getAllCategories()
    {
        $categories = Article::select('kategori')->distinct()->pluck('kategori');
        return response()->json($categories);
    }

    public function searchArticles(Request $request)
    {
        $query = Article::with(['usersWhoLiked', 'comments']);

        if ($request->has('keyword')) {
            $keyword = $request->input('keyword');
            $query->where('judul', 'like', '%' . $keyword . '%');
                  //->orWhere('isi_artikel', 'like', '%' . $keyword . '%');
        }

        return response()->json($query->get());
    }
}
