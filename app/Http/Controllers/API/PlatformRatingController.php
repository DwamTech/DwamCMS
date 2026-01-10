<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PlatformRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlatformRatingController extends Controller
{
    public function index()
    {
        $avg = PlatformRating::avg('rating');
        $count = PlatformRating::count();

        return response()->json([
            'average_rating' => $avg ? round($avg, 1) : 0, // Current average
            'rating_count' => $count,
            'max_rating' => 5,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Spam Protection: Check if this IP rated in the last 1 minute
        $ip = $request->ip();
        $recentRating = PlatformRating::where('ip_address', $ip)
            ->where('created_at', '>', now()->subMinute())
            ->exists();

        // Uncomment the lines below to ENFORCE restriction.
        // For now, I will leave it loose for easier testing, or maybe return a friendly message?
        // Let's enforce it to show "Logic".
        if ($recentRating) {
            return response()->json(['message' => 'عذراً، لقد قمت بالتقييم مؤخراً. يرجى الانتظار قليلاً.'], 429);
        }

        PlatformRating::create([
            'rating' => $request->rating,
            'ip_address' => $ip,
            'user_agent' => $request->userAgent(),
        ]);

        // Return updated stats immediately
        return $this->index();
    }
}
