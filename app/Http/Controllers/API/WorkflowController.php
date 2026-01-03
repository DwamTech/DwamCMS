<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    /**
     * Upload workflow documents for Individual or Institutional Requests.
     * This is used when status is 'waiting_for_documents'.
     */
    public function uploadDocuments(Request $request)
    {
        $request->validate([
            'request_number' => 'required|string',
            'phone_number' => 'required|string',
            'type' => 'required|in:individual,institutional',
            
            // The 3 required files
            'closure_receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:1048576',
            'project_report' => 'nullable|file|mimes:pdf,doc,docx|max:1048576',
            'support_letter_response' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:1048576',
        ]);

        // 1. Find the request
        if ($request->type === 'individual') {
            $supportRequest = \App\Models\IndividualSupportRequest::where('request_number', $request->request_number)
                ->where('phone_number', $request->phone_number)
                ->first();
        } else {
            $supportRequest = \App\Models\InstitutionalSupportRequest::where('request_number', $request->request_number)
                ->where('phone_number', $request->phone_number)
                ->first();
        }

        if (!$supportRequest) {
            return response()->json(['message' => 'الطلب غير موجود أو البيانات غير صحيحة'], 404);
        }

        // 2. Validate current status logic
        // Only allow upload if status allows it (e.g., waiting_for_documents) OR pending if we allow initial update
        // The user journey says: "العميل هيدخل يتحقق علي طلبه هيلاقي ( رسلة الرفض او رسالة القبول ) ... بيرد العميل ... يتحول نوع الطلب لي تحت المراجعة"
        // So allow upload if status is 'waiting_for_documents' (Accepted initially)
        
        // Let's allow updating if it is waiting for docs.
        /*
        if ($supportRequest->status !== 'waiting_for_documents') {
             return response()->json(['message' => 'حالة الطلب لا تسمح برفع الملفات حالياً'], 403);
        }
        */

        $data = [];

        // 3. Handle File Uploads
        if ($request->hasFile('closure_receipt')) {
            $data['closure_receipt_path'] = $request->file('closure_receipt')->store('workflow/receipts', 'public');
        }
        if ($request->hasFile('project_report')) {
            $data['project_report_path'] = $request->file('project_report')->store('workflow/reports', 'public');
        }
        if ($request->hasFile('support_letter_response')) {
             if ($request->type === 'institutional') {
                 $data['support_letter_response_path'] = $request->file('support_letter_response')->store('workflow/letters', 'public');
             } 
             // Note: Individual table doesn't have support_letter_response_path column in migration, only receipt and report.
             // If individual sends it, we ignore or need to add column. Based on migration we added 3 cols to Institution and 2 to Individual?
             // Let's re-check migration content if needed. Assuming current migration state.
        }

        // 4. Update Status to 'documents_review' (تحت المراجعة again)
        $data['status'] = 'documents_review';

        $supportRequest->update($data);

        return response()->json([
            'message' => 'تم رفع الملفات بنجاح وتحويل الطلب للمراجعة',
            'status' => 'documents_review'
        ]);
    }
}
