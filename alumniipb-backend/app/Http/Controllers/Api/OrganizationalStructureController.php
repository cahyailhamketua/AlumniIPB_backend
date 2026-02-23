<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrganizationalStructure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class OrganizationalStructureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $structures = OrganizationalStructure::all();
        return response()->json(['organizational_structures' => $structures]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'position' => 'required|string',
            'tenure' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->only(['name', 'position', 'tenure']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('organizational_structures', 'public');
            $data['image'] = 'storage/' . $path;
        }

        $structure = OrganizationalStructure::create($data);

        return response()->json([
            'message' => 'Organizational structure created successfully',
            'organizational_structure' => $structure,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $structure = OrganizationalStructure::find($id);
        if (!$structure) {
            return response()->json(['message' => 'Organizational structure not found'], 404);
        }

        return response()->json(['organizational_structure' => $structure]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $structure = OrganizationalStructure::find($id);
        if (!$structure) {
            return response()->json(['message' => 'Organizational structure not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'position' => 'required|string',
            'tenure' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $structure->fill($request->only(['name', 'position', 'tenure']));

        if ($request->hasFile('image')) {
            $this->deleteOrganizationalStructureImage($structure->image);

            $path = $request->file('image')->store('organizational_structures', 'public');
            $structure->image = 'storage/' . $path;
        }

        $structure->save();

        return response()->json([
            'message' => 'Organizational structure updated successfully',
            'organizational_structure' => $structure,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $structure = OrganizationalStructure::find($id);
        if (!$structure) {
            return response()->json(['message' => 'Organizational structure not found'], 404);
        }

        $this->deleteOrganizationalStructureImage($structure->image);

        $structure->delete();

        return response()->json(['message' => 'Organizational structure deleted successfully'], 200);
    }

    /**
     * Hapus file image dari storage (format storage/... atau legacy images/...).
     */
    private function deleteOrganizationalStructureImage(?string $image): void
    {
        if (!$image) {
            return;
        }

        if (Str::startsWith($image, 'storage/')) {
            $path = Str::after($image, 'storage/');
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            return;
        }

        // Legacy: disimpan di public/images/organizational_structures
        $path = ltrim($image, '/');
        if (str_starts_with($path, 'images/organizational_structures/') && file_exists(public_path($path))) {
            @unlink(public_path($path));
        }
    }
}

