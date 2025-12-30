<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    // Admin: List all feedback (optionally filter by type)
    public function index(Request $request)
    {
        $query = Feedback::query();

        if ($request->has('type') && in_array($request->type, ['suggestion', 'complaint'])) {
            $query->where('type', $request->type);
        }

        $feedback = $query->latest()->paginate(20);
        return response()->json($feedback);
    }

    // Public: Submit feedback
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
            'type' => 'required|in:suggestion,complaint',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Feedback::create($validator->validated());

        // Custom message based on type
        $message = $request->type == 'complaint' 
            ? 'تم استلام شكواك وسنعمل على حلها قريباً.' 
            : 'شكراً لمقترحك، نسعد بمساهمتك.';

        return response()->json(['message' => $message], 201);
    }
}
