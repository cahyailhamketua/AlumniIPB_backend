<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrganizationalStructure;
use Illuminate\Http\Request;
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
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images/organizational_structures'), $imageName);
            $data['image'] = '/images/organizational_structures/'.$imageName;
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
            if ($structure->image && file_exists(public_path($structure->image))) {
                @unlink(public_path($structure->image));
            }

            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images/organizational_structures'), $imageName);
            $structure->image = '/images/organizational_structures/'.$imageName;
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

        if ($structure->image && file_exists(public_path($structure->image))) {
            @unlink(public_path($structure->image));
        }

        $structure->delete();

        return response()->json(['message' => 'Organizational structure deleted successfully'], 200);
    }
}

