<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Timeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TimelineController extends Controller
{
    //

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $timelines = Timeline::all();
        return response()->json(['timelines' => $timelines]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer',
            'kegiatan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $timeline = Timeline::create($request->all());
        return response()->json(['message' => 'Timeline created successfully', 'timeline' => $timeline], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $timeline = Timeline::find($id);
        if (!$timeline) {
            return response()->json(['message' => 'Timeline not found'], 404);
        }
        return response()->json(['timeline' => $timeline]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $timeline = Timeline::find($id);
        if (!$timeline) {
            return response()->json(['message' => 'Timeline not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer',
            'kegiatan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $timeline->update($request->all());
        return response()->json(['message' => 'Timeline updated successfully', 'timeline' => $timeline], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $timeline = Timeline::find($id);
        if (!$timeline) {
            return response()->json(['message' => 'Timeline not found'], 404);
        }

        $timeline->delete();
        return response()->json(['message' => 'Timeline deleted successfully'], 200);
    }
}
