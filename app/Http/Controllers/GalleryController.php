<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGalleryRequest;
use App\Http\Requests\UpdateGalleryRequest;
use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $query = Gallery::with(['section', 'user', 'images']);

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('author')) {
            $query->where('user_id', $request->author);
        }

        $galleries = $query->latest()->paginate(15);

        return response()->json($galleries);
    }

    public function store(StoreGalleryRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        if (empty($data['section_id'])) {
            $defaultSection = Section::where('slug', 'general')->first();
            if ($defaultSection) {
                $data['section_id'] = $defaultSection->id;
            }
        }

        if ($request->hasFile('image')) {
            $data['cover_image'] = $request->file('image')->store('galleries/covers', 'public');
        }

        unset($data['image'], $data['images'], $data['delete_image_ids']);

        $gallery = Gallery::create($data);

        $this->syncImagesFromRequest($request, $gallery);

        $gallery->load(['section', 'user', 'images']);

        return response()->json([
            'message' => 'Gallery created successfully',
            'gallery' => $gallery,
        ], 201);
    }

    public function show($id)
    {
        $gallery = Gallery::with(['section', 'user', 'images'])->findOrFail($id);
        $gallery->increment('views_count');

        return response()->json($gallery);
    }

    public function update(UpdateGalleryRequest $request, Gallery $gallery)
    {
        $data = $request->validated();

        if (array_key_exists('section_id', $data) && empty($data['section_id'])) {
            $defaultSection = Section::where('slug', 'general')->first();
            if ($defaultSection) {
                $data['section_id'] = $defaultSection->id;
            }
        }

        if ($request->hasFile('image')) {
            if ($gallery->cover_image) {
                Storage::disk('public')->delete($gallery->getRawOriginal('cover_image'));
            }

            $data['cover_image'] = $request->file('image')->store('galleries/covers', 'public');
        }

        $deleteImageIds = $data['delete_image_ids'] ?? [];

        unset($data['image'], $data['images'], $data['delete_image_ids']);

        $gallery->update($data);

        if (! empty($deleteImageIds)) {
            $this->deleteImagesByIds($gallery, $deleteImageIds);
        }

        $this->syncImagesFromRequest($request, $gallery);

        $gallery->load(['section', 'user', 'images']);

        return response()->json([
            'message' => 'Gallery updated successfully',
            'gallery' => $gallery,
        ]);
    }

    public function destroy(Gallery $gallery)
    {
        if ($gallery->cover_image) {
            Storage::disk('public')->delete($gallery->getRawOriginal('cover_image'));
        }

        foreach ($gallery->images as $image) {
            Storage::disk('public')->delete($image->getRawOriginal('image_path'));
        }

        $gallery->delete();

        return response()->json([
            'message' => 'Gallery deleted successfully',
        ]);
    }

    private function syncImagesFromRequest(Request $request, Gallery $gallery): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $files = $request->file('images');
        if (! is_array($files)) {
            return;
        }

        $currentMaxOrder = (int) $gallery->images()->max('sort_order');
        $order = $currentMaxOrder + 1;

        foreach ($files as $file) {
            if (! $file) {
                continue;
            }

            $path = $file->store('galleries/images', 'public');

            GalleryImage::create([
                'gallery_id' => $gallery->id,
                'image_path' => $path,
                'sort_order' => $order,
            ]);

            $order++;
        }

        if (! $gallery->getRawOriginal('cover_image')) {
            $first = $gallery->images()->orderBy('sort_order')->first();
            if ($first) {
                $gallery->update(['cover_image' => $first->getRawOriginal('image_path')]);
            }
        }
    }

    private function deleteImagesByIds(Gallery $gallery, array $ids): void
    {
        $images = $gallery->images()->whereIn('id', $ids)->get();

        foreach ($images as $image) {
            Storage::disk('public')->delete($image->getRawOriginal('image_path'));
            $image->delete();
        }
    }
}
