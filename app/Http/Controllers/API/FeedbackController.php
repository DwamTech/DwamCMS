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
            'name' => 'required|string|max:1048576',
            'email' => 'required|email|max:1048576',
            'phone_number' => 'nullable|string|max:20',
            'message' => 'required|string',
            'attachment_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:1048576', // 5MB max
            'type' => 'required|in:suggestion,complaint',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('attachment_path')) {
            $data['attachment_path'] = $request->file('attachment_path')->store('feedback_attachments', 'public');
        }

        Feedback::create($data);

        // Custom message based on type
        $message = $request->type == 'complaint'
            ? 'تم استلام شكواك وسنعمل على حلها قريباً.'
            : 'شكراً لمقترحك، نسعد بمساهمتك.';

        return response()->json(['message' => $message], 201);
    }

    // Admin: Delete feedback
    public function destroy($id)
    {
        $feedback = Feedback::find($id);
        if (! $feedback) {
            return response()->json(['message' => 'الرسالة غير موجودة'], 404);
        }

        $feedback->delete();

        return response()->json(['message' => 'تم حذف الرسالة بنجاح']);
    }
}
