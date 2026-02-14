<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobOpening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class JobOpeningController extends Controller
{
    // Daftar lowongan: hanya yang active dan belum lewat deadline untuk guest/user biasa
    public function index(Request $request)
    {
        $query = JobOpening::query();

        // common query params
        $search = $request->query('query');
        $industry = $request->query('industry');
        $position = $request->query('position');
        $sort = $request->query('sort', 'latest'); // latest | deadline
        $type = $request->query('type');

        // Ambil user terautentikasi jika ada (coba beberapa guard supaya Bearer token dikenali pada route publik)
        $user = $this->getAuthenticatedUser($request);

        // Admin can see everything and apply admin-specific filters
        if ($this->isAdmin($user)) {
            $active = $request->query('active'); // '1' or '0' atau null
            $expired = $request->query('expired'); // '1' or '0' atau null

            if (!is_null($active)) {
                $query->where('active', $active ? true : false);
            }

            if (!is_null($expired)) {
                if ($expired) {
                    $query->whereNotNull('deadline')->where('deadline', '<', now());
                } else {
                    $query->where(function ($q) {
                        $q->whereNull('deadline')->orWhere('deadline', '>', now());
                    });
                }
            }
        } else {
            // Guest dan alumni hanya melihat lowongan active yang belum lewat deadline
            $query->where('active', true)
                ->where(function ($q) {
                    $q->whereNull('deadline')->orWhere('deadline', '>', now());
                });
        }

        // optional type filter (job | internship)
        if (!is_null($type) && in_array($type, ['job','internship'])) {
            $query->where('type', $type);
        }

        // search across some text fields
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('position', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // filter by industry (stored as string in model)
        if ($industry) {
            $query->where('industry', $industry);
        }

        // filter by position string (exact or partial)
        if ($position) {
            $query->where('position', 'like', "%{$position}%");
        }

        // sorting
        if ($sort === 'deadline') {
            // ensure items without deadline appear last
            $query->orderByRaw("IFNULL(deadline, '9999-12-31') ASC");
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = (int) $request->query('per_page', 15);
        $items = $query->paginate($perPage)->appends($request->query());

        return response()->json($items);
    }

    public function show($id)
    {
        $item = JobOpening::findOrFail($id);

        // Jika bukan admin, cek status active dan deadline
        $user = $this->getAuthenticatedUser(request());
        if (! $this->isAdmin($user)) {
            if (! $item->active || ($item->deadline && $item->deadline->isPast())) {
                return response()->json(['message' => 'Not found'], 404);
            }
        }

        return response()->json($item);
    }

    /**
     * Return distinct industries for dropdowns (sorted, non-empty)
     */
    public function industries()
    {
        $items = JobOpening::query()
            ->whereNotNull('industry')
            ->where('industry', '<>', '')
            ->distinct()
            ->orderBy('industry')
            ->pluck('industry');

        return response()->json($items);
    }

    /**
     * Return distinct positions for dropdowns (sorted, non-empty)
     */
    public function positions()
    {
        $items = JobOpening::query()
            ->whereNotNull('position')
            ->where('position', '<>', '')
            ->distinct()
            ->orderBy('position')
            ->pluck('position');

        return response()->json($items);
    }

    // Membuat lowongan. Hanya untuk user terautentikasi (alumni/admin)
    public function store(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'position' => 'required|string|max:255',
            'description' => 'nullable|string',
            'industry' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'type' => 'required|in:internship,job',
            'deadline' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'salary_min' => 'nullable|numeric',
            'salary_max' => 'nullable|numeric',
            'requirements' => 'nullable|array',
            'requirements.*' => 'string',
            'link' => 'required|url',
        ]);

        $data['created_by_id'] = $user->id;
        $data['created_by_type'] = $user->role ?? 'alumni';

        // Jika admin membuat, langsung active dan approved
        if ($this->isAdmin($user)) {
            $data['active'] = true;
            $data['approved_by_id'] = $user->id;
            $data['approved_at'] = now();
        } else {
            // alumni -> perlu approval admin
            $data['active'] = false;
        }

        // Handle uploaded image file and store under public disk
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('job_openings', 'public');
            // store path as accessible URL under /storage
            $data['image'] = 'storage/' . $path;
        }

        $job = JobOpening::create($data);

        return response()->json($job, 201);
    }

    // Update lowongan: pemilik (created_by_id) atau admin
    public function update(Request $request, $id)
    {
        $user = $request->user();
        if (! $user) return response()->json(['message'=>'Unauthorized'], 401);

        $job = JobOpening::findOrFail($id);

        if (! ($user->id === $job->created_by_id || $this->isAdmin($user))) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'position' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'industry' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'type' => 'sometimes|required|in:internship,job',
            'deadline' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'salary_min' => 'nullable|numeric',
            'salary_max' => 'nullable|numeric',
            'requirements' => 'nullable|array',
            'requirements.*' => 'string',
            'link' => 'sometimes|required|url',
            'active' => 'nullable|boolean'
        ]);

        // Jika non-admin mencoba meng-set active => ignore
        if (isset($data['active']) && $data['active'] && ! $this->isAdmin($user)) {
            unset($data['active']);
        }

        // Jika ada file image baru, simpan dan hapus file lama bila ada
        if ($request->hasFile('image')) {
            // Hapus file lama jika disimpan di storage/public
            if ($job->image) {
                $old = $job->image;
                if (Str::startsWith($old, 'storage/')) {
                    $oldPath = Str::after($old, 'storage/');
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
            }

            $file = $request->file('image');
            $path = $file->store('job_openings', 'public');
            $data['image'] = 'storage/' . $path;
        }

        $job->fill($data);
        $job->save();

        return response()->json($job);
    }

    // Approve lowongan (admin only)
    public function approve(Request $request, $id)
    {
        $user = $request->user();
        if (! $this->isAdmin($user)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $job = JobOpening::findOrFail($id);
        $job->active = true;
        $job->approved_by_id = $user->id;
        $job->approved_at = now();
        $job->save();

        return response()->json($job);
    }

    // Menonaktifkan lowongan (admin only)
    public function deactivate(Request $request, $id)
    {
        $user = $request->user();
        if (! $this->isAdmin($user)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $job = JobOpening::findOrFail($id);
        $job->active = false;
        $job->save();

        return response()->json($job);
    }

    // Hapus lowongan (admin only)
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if (! $this->isAdmin($user)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $job = JobOpening::findOrFail($id);

        // Hapus image dari storage jika ada
        if ($job->image) {
            $old = $job->image;
            if (Str::startsWith($old, 'storage/')) {
                $oldPath = Str::after($old, 'storage/');
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
        }

        $job->delete();

        return response()->json(['message' => 'Deleted'], 200);
    }

    /**
     * Determine if given authenticated object represents an admin.
     * Accepts User with role 'admin', objects with isAdmin(), or Admin model.
     */
    private function isAdmin($user): bool
    {
        if (! $user) return false;
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) return true;
        if (isset($user->role) && $user->role === 'admin') return true;
        if ($user instanceof \App\Models\Admin) return true;
        return false;
    }

    /**
     * Try to obtain authenticated user from request or common guards.
     */
    private function getAuthenticatedUser(Request $request)
    {
        // Try request user (default guard)
        $user = $request->user();
        if ($user) return $user;

        // Try Sanctum guard
        try {
            $user = $request->user('sanctum');
            if ($user) return $user;
        } catch (\Exception $e) {
            // ignore
        }

        // Try Auth facade using sanctum
        try {
            $user = Auth::guard('sanctum')->user();
            if ($user) return $user;
        } catch (\Exception $e) {
            // ignore
        }

        // Fallback to Auth::user()
        return Auth::user();
    }
}
