<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\IndividualSupportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class IndividualSupportRequestController extends Controller
{
    public function store(Request $request)
    {
        // Check if enabled
        $isEnabled = \App\Models\SupportSetting::where('key', 'individual_support_enabled')->value('value');
        if ($isEnabled !== 'true') {
             return response()->json(['message' => 'عذراً، التقديم على طلبات دعم الأفراد مغلق حالياً.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'nationality' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'housing_type' => 'required|string|max:255',
            'housing_type_other' => 'nullable|required_if:housing_type,أخرى,other|string|max:255',
            'identity_image_path' => 'required|file|image|max:5120', // 5MB max
            'birth_date' => 'required|date',
            'identity_expiry_date' => 'required|date',
            'phone_number' => 'required|string|max:20',
            'whatsapp_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'academic_qualification_path' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'scientific_activity' => 'required|string|max:255',
            'scientific_activity_other' => 'nullable|required_if:scientific_activity,أخرى,other|string|max:255',
            'cv_path' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'workplace' => 'required|string|max:255',
            'support_scope' => 'required|in:full,partial',
            'amount_requested' => 'required|numeric|min:0',
            'support_type' => 'required|string|max:255',
            'support_type_other' => 'nullable|required_if:support_type,أخرى,other|string|max:255',
            'has_income' => 'required|boolean', // 0 or 1, true or false
            'income_source' => 'nullable|required_if:has_income,true,1|string|max:255',
            'marital_status' => 'required|in:single,married',
            'family_members_count' => 'nullable|required_if:marital_status,married|integer|min:0',
            'recommendation_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'bank_account_iban' => 'required|string|max:50',
            'bank_name' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $data = $validator->validated();

            // Handle File Uploads
            $fileFields = [
                'identity_image_path', 
                'academic_qualification_path', 
                'cv_path', 
                'recommendation_path'
            ];

            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    // Store in a specific directory, e.g., storage/app/public/support_requests/{field}
                    $path = $request->file($field)->store('support_requests/' . $field, 'public');
                    $data[$field] = $path;
                }
            }

            // Generate Request Number (e.g., SUP-YYYY-RANDOM)
            $data['request_number'] = 'SUP-' . date('Y') . '-' . strtoupper(Str::random(6));

            $supportRequest = IndividualSupportRequest::create($data);

            return response()->json([
                'message' => 'تم استلام طلبك بنجاح',
                'request_number' => $supportRequest->request_number,
                'phone_number' => $supportRequest->phone_number,
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ أثناء معالجة الطلب', 'error' => $e->getMessage()], 500);
        }
    }

    public function checkStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'request_number' => 'required|string',
            'phone_number' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $supportRequest = IndividualSupportRequest::where('request_number', $request->request_number)
            ->where('phone_number', $request->phone_number)
            ->first();

        if (!$supportRequest) {
            return response()->json(['message' => 'الطلب غير موجود أو البيانات غير صحيحة'], 404);
        }

        return response()->json([
            'status' => $supportRequest->status,
            'rejection_reason' => $supportRequest->rejection_reason,
            'created_at' => $supportRequest->created_at->format('Y-m-d'),
        ], 200);
    }

    // --- Admin Criteria Methods ---

    public function index(Request $request)
    {
        $query = IndividualSupportRequest::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('request_number', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $requests = $query->latest()->paginate(20);
        return response()->json($requests);
    }

    public function show($id)
    {
        $request = IndividualSupportRequest::find($id);
        if (!$request) {
            return response()->json(['message' => 'الطلب غير موجود'], 404);
        }
        return response()->json($request);
    }

    public function update(Request $request, $id)
    {
        $supportRequest = IndividualSupportRequest::find($id);
        if (!$supportRequest) {
            return response()->json(['message' => 'الطلب غير موجود'], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,accepted,rejected',
            'rejection_reason' => 'nullable|required_if:status,rejected|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $supportRequest->update([
            'status' => $request->status,
            'rejection_reason' => $request->status == 'rejected' ? $request->rejection_reason : null,
        ]);

        return response()->json(['message' => 'تم تحديث حالة الطلب بنجاح', 'data' => $supportRequest]);
    }

    public function destroy($id)
    {
        $supportRequest = IndividualSupportRequest::find($id);
        if (!$supportRequest) {
            return response()->json(['message' => 'الطلب غير موجود'], 404);
        }

        // Ideally we should delete files too, keeping it simple for now or use observers
        $supportRequest->delete();

        return response()->json(['message' => 'تم حذف الطلب بنجاح']);
    }
}
