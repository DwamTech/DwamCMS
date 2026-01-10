<?php

namespace App\Http\Controllers;

use App\Models\Section;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections = Section::where('is_active', true)->get();

        return response()->json($sections);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $section = Section::where('is_active', true)->findOrFail($id);

        return response()->json($section);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:1048576|unique:sections,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $section = Section::create([
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
            'user_id' => $request->user()->id ?? null, // Optional if we want to track creator
        ]);

        return response()->json([
            'message' => 'Section created successfully',
            'section' => $section,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\Illuminate\Http\Request $request, Section $section)
    {
        $request->validate([
            'name' => 'sometimes|string|max:1048576|unique:sections,name,'.$section->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['name', 'description', 'is_active']);

        if ($request->has('name')) {
            $data['slug'] = \Illuminate\Support\Str::slug($request->name);
        }

        $section->update($data);

        return response()->json([
            'message' => 'Section updated successfully',
            'section' => $section,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Section $section)
    {
        // Optional: Check if section has articles before deleting?
        // For now, let's allow deletion.
        $section->delete();

        return response()->json([
            'message' => 'Section deleted successfully',
        ]);
    }
}
