<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\InstitutionalSupportRequest;
use App\Models\SupportSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InstitutionalSupportRequestController extends Controller
{
    public function store(Request $request)
    {
        // Check if enabled
        $isEnabled = SupportSetting::where('key', 'institutional_support_enabled')->value('value');
        if ($isEnabled !== 'true') {
            return response()->json(['message' => 'عذراً، التقديم على طلبات دعم المؤسسات مغلق حالياً.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'institution_name' => 'required|string|max:1048576',
            'license_number' => 'required|string|max:1048576',
            'license_certificate_path' => 'required|file|mimes:pdf,jpg,jpeg,png|max:1048576',
            'email' => 'required|email|max:1048576',
            'support_letter_path' => 'required|file|mimes:pdf,jpg,jpeg,png|max:1048576',
            'phone_number' => 'required|string|max:20',
            'ceo_name' => 'required|string|max:1048576',
            'ceo_mobile' => 'required|string|max:20',
            'whatsapp_number' => 'required|string|max:20',
            'city' => 'required|string|max:1048576',
            'activity_type' => 'required|string|max:1048576',
            'activity_type_other' => 'nullable|required_if:activity_type,أخرى,other|string|max:1048576',
            'project_name' => 'required|string|max:1048576',
            'project_type' => 'required|string|max:1048576',
            'project_type_other' => 'nullable|required_if:project_type,أخرى,other|string|max:1048576',
            'project_file_path' => 'required|file|mimes:pdf,doc,docx|max:1048576', // 10MB
            'project_manager_name' => 'required|string|max:1048576',
            'project_manager_mobile' => 'required|string|max:20',
            'goal_1' => 'required|string|max:1048576',
            'goal_2' => 'nullable|string|max:1048576',
            'goal_3' => 'nullable|string|max:1048576',
            'goal_4' => 'nullable|string|max:1048576',
            'other_goals' => 'nullable|string',
            'beneficiaries' => 'required|string|max:1048576',
            'beneficiaries_other' => 'nullable|required_if:beneficiaries,أخرى,other|string|max:1048576',
            'project_cost' => 'required|numeric|min:0',
            'project_outputs' => 'required|string',
            'operational_plan_path' => 'required|file|mimes:pdf,doc,docx|max:1048576',
            'support_scope' => 'required|in:full,partial',
            'amount_requested' => 'required|numeric|min:0',
            'account_name' => 'required|string|max:1048576',
            'bank_account_iban' => 'required|string|max:50',
            'bank_name' => 'required|string|max:1048576',
            'bank_certificate_path' => 'required|file|mimes:pdf,jpg,jpeg,png|max:1048576',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $data = $validator->validated();

            // Handle File Uploads
            $fileFields = [
                'license_certificate_path',
                'support_letter_path',
                'project_file_path',
                'operational_plan_path',
                'bank_certificate_path',
            ];

            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store('institutional_requests/'.$field, 'public');
                    $data[$field] = $path;
                }
            }

            // Generate Request Number (e.g., ORG-YYYY-RANDOM)
            // Generate Sequential Request Number (0000, 0001, ...)
            $lastRequest = InstitutionalSupportRequest::latest('id')->first();
            $nextId = $lastRequest ? $lastRequest->id + 1 : 1;
            // Pad with zeros, ensuring at least 4 digits, but expanding if larger (e.g., 10000)
            $data['request_number'] = str_pad($nextId, 4, '0', STR_PAD_LEFT);

            $requestObj = InstitutionalSupportRequest::create($data);

            return response()->json([
                'message' => 'تم استلام طلب المؤسسة بنجاح',
                'request_number' => $requestObj->request_number,
                'phone_number' => $requestObj->phone_number,
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

        $requestObj = InstitutionalSupportRequest::where('request_number', $request->request_number)
            ->where('phone_number', $request->phone_number)
            ->first();

        if (! $requestObj) {
            return response()->json(['message' => 'الطلب غير موجود أو البيانات غير صحيحة'], 404);
        }

        return response()->json([
            'status' => $requestObj->status,
            'rejection_reason' => $requestObj->rejection_reason,
            'admin_response_message' => $requestObj->admin_response_message, // Field for generic admin messages
            'created_at' => $requestObj->created_at->format('Y-m-d'),
        ], 200);
    }

    // --- Admin Criteria Methods ---

    public function index(Request $request)
    {
        $query = InstitutionalSupportRequest::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('institution_name', 'like', "%{$search}%")
                    ->orWhere('request_number', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $requests = $query->latest()->paginate(20);

        return response()->json($requests);
    }

    public function show($id)
    {
        $request = InstitutionalSupportRequest::find($id);
        if (! $request) {
            return response()->json(['message' => 'الطلب غير موجود'], 404);
        }

        return response()->json($request);
    }

    public function update(Request $request, $id)
    {
        $supportRequest = InstitutionalSupportRequest::find($id);
        if (! $supportRequest) {
            return response()->json(['message' => 'الطلب غير موجود'], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,waiting_for_documents,documents_review,completed,rejected,archived',
            'rejection_reason' => 'nullable|required_if:status,rejected|string',
            'admin_response_message' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $updateData = [
            'status' => $request->status,
        ];

        // Save rejection_reason if provided (e.g. for rejected OR waiting_for_documents)
        if ($request->has('rejection_reason')) {
            $updateData['rejection_reason'] = $request->rejection_reason;
        } elseif ($request->status == 'rejected') {
            $updateData['rejection_reason'] = $request->rejection_reason;
        }

        if ($request->has('admin_response_message') || isset($request->admin_response_message)) {
            $updateData['admin_response_message'] = $request->admin_response_message;
        }

        $supportRequest->update($updateData);

        return response()->json(['message' => 'تم تحديث حالة الطلب بنجاح', 'data' => $supportRequest]);
    }

    public function destroy($id)
    {
        $supportRequest = InstitutionalSupportRequest::find($id);
        if (! $supportRequest) {
            return response()->json(['message' => 'الطلب غير موجود'], 404);
        }

        $supportRequest->delete();

        return response()->json(['message' => 'تم حذف الطلب بنجاح']);
    }
}
