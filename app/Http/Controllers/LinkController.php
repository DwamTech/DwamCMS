<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLinkRequest;
use App\Http\Requests\UpdateLinkRequest;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LinkController extends Controller
{
    public function index(Request $request)
    {
        $query = Link::with(['section', 'user']);

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('author')) {
            $query->where('user_id', $request->author);
        }

        $links = $query->latest()->paginate(15);

        return response()->json($links);
    }

    public function store(StoreLinkRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('links/images', 'public');
        }

        unset($data['image']);

        $link = Link::create($data);

        return response()->json([
            'message' => 'Link created successfully',
            'link' => $link,
        ], 201);
    }

    public function show($id)
    {
        $link = Link::with(['section', 'user'])->findOrFail($id);
        $link->increment('views_count');

        return response()->json($link);
    }

    public function update(UpdateLinkRequest $request, Link $link)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($link->getRawOriginal('image_path')) {
                Storage::disk('public')->delete($link->getRawOriginal('image_path'));
            }

            $data['image_path'] = $request->file('image')->store('links/images', 'public');
        }

        unset($data['image']);

        $link->update($data);

        return response()->json([
            'message' => 'Link updated successfully',
            'link' => $link,
        ]);
    }

    public function destroy(Link $link)
    {
        if ($link->getRawOriginal('image_path')) {
            Storage::disk('public')->delete($link->getRawOriginal('image_path'));
        }

        $link->delete();

        return response()->json([
            'message' => 'Link deleted successfully',
        ]);
    }
}
