<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use App\Models\OrganizationalStructure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AboutUsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $aboutUs = AboutUs::with('organizationalStructures')->first();
        return response()->json(['about_us' => $aboutUs]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'history' => 'nullable|string',
            'mission_focus' => 'nullable|string',
            'contact' => 'nullable|string',
            'address' => 'nullable|string',
            'gmail' => 'nullable|email',
            'instagram' => 'nullable|string',
            'youtube' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $aboutUs = AboutUs::firstOrNew();
        $aboutUs->history = $request->history;
        $aboutUs->mission_focus = $request->mission_focus;
        $aboutUs->contact = $request->contact;
        $aboutUs->address = $request->address;
        $aboutUs->gmail = $request->gmail;
        $aboutUs->instagram = $request->instagram;
        $aboutUs->youtube = $request->youtube;

        $aboutUs->save();

        return response()->json(['message' => 'About Us information saved successfully', 'about_us' => $aboutUs], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $aboutUs = AboutUs::with('organizationalStructures')->first();
        if (!$aboutUs) {
            return response()->json(['message' => 'About Us information not found'], 404);
        }
        return response()->json(['about_us' => $aboutUs]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $aboutUs = AboutUs::first();
        if (!$aboutUs) {
            return response()->json(['message' => 'About Us information not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'history' => 'nullable|string',
            'mission_focus' => 'nullable|string',
            'contact' => 'nullable|string',
            'address' => 'nullable|string',
            'gmail' => 'nullable|email',
            'instagram' => 'nullable|string',
            'youtube' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $aboutUs->history = $request->history;
        $aboutUs->mission_focus = $request->mission_focus;
        $aboutUs->contact = $request->contact;
        $aboutUs->address = $request->address;
        $aboutUs->gmail = $request->gmail;
        $aboutUs->instagram = $request->instagram;
        $aboutUs->youtube = $request->youtube;

        $aboutUs->save();

        return response()->json(['message' => 'About Us information updated successfully', 'about_us' => $aboutUs], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        $aboutUs = AboutUs::first();
        if (!$aboutUs) {
            return response()->json(['message' => 'About Us information not found'], 404);
        }

        $aboutUs->delete();

        return response()->json(['message' => 'About Us information deleted successfully'], 200);
    }

    // Organizational Structure CRUD operations

    public function getOrganizationalStructures()
    {
        $aboutUs = AboutUs::first();
        if (!$aboutUs) {
            return response()->json(['message' => 'About Us information not found'], 404);
        }
        return response()->json(['organizational_structures' => $aboutUs->organizationalStructures]);
    }

    public function addOrganizationalStructure(Request $request)
    {
        $aboutUs = AboutUs::first();
        if (!$aboutUs) {
            return response()->json(['message' => 'About Us information not found, please create it first'], 404);
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

        $organizationalStructure = new OrganizationalStructure($request->all());

        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images/organizational_structures'), $imageName);
            $organizationalStructure->image = '/images/organizational_structures/'.$imageName;
        }

        $aboutUs->organizationalStructures()->save($organizationalStructure);

        return response()->json(['message' => 'Organizational structure added successfully', 'organizational_structure' => $organizationalStructure], 201);
    }

    public function updateOrganizationalStructure(Request $request, string $id)
    {
        $organizationalStructure = OrganizationalStructure::find($id);
        if (!$organizationalStructure) {
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

        $organizationalStructure->fill($request->all());

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($organizationalStructure->image && file_exists(public_path($organizationalStructure->image))) {
                unlink(public_path($organizationalStructure->image));
            }
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images/organizational_structures'), $imageName);
            $organizationalStructure->image = '/images/organizational_structures/'.$imageName;
        }

        $organizationalStructure->save();

        return response()->json(['message' => 'Organizational structure updated successfully', 'organizational_structure' => $organizationalStructure], 200);
    }

    public function deleteOrganizationalStructure(string $id)
    {
        $organizationalStructure = OrganizationalStructure::find($id);
        if (!$organizationalStructure) {
            return response()->json(['message' => 'Organizational structure not found'], 404);
        }

        // Delete image if exists
        if ($organizationalStructure->image && file_exists(public_path($organizationalStructure->image))) {
            unlink(public_path($organizationalStructure->image));
        }

        $organizationalStructure->delete();

        return response()->json(['message' => 'Organizational structure deleted successfully'], 200);
    }
}
