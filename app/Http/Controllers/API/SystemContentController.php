<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SystemContentController extends Controller
{
    /**
     * Get content by key (Public).
     * Keys: about_waqf, masaref_alre3
     */
    public function show($key)
    {
        $content = \App\Models\SystemContent::where('key', $key)->first();

        if (!$content) {
            // Return empty or default structure if not found to avoid 404 block for frontend
            return response()->json([
                'key' => $key,
                'content' => '',
                'exists' => false
            ]);
        }

        return response()->json($content);
    }

    /**
     * Update content by key (Admin).
     */
    public function update(Request $request, $key)
    {
        $request->validate([
            'content' => 'required|string', // HTML string
        ]);

        $content = \App\Models\SystemContent::updateOrCreate(
            ['key' => $key],
            ['content' => $request->content]
        );

        return response()->json([
            'message' => 'تم تحديث المحتوى بنجاح',
            'data' => $content
        ]);
    }
}
