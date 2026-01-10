<?php

use App\Models\InstitutionalSupportRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "--- Starting Support Workflow Test ---\n";

// 1. Setup Admin User
$admin = User::where('email', 'admin@dwam.com')->first();
if (! $admin) {
    exit("Error: Admin user not found. Run seeders first.\n");
}
Auth::login($admin);
echo '[+] Logged in as Admin: '.$admin->name."\n";

// 2. Create Initial Request (Simulating Client Submission)
// We create directly to focus on workflow status transitions
$requestNumber = 'TEST-'.rand(1000, 9999);
$phone = '0500000000';

$supportRequest = InstitutionalSupportRequest::create([
    'request_number' => $requestNumber,
    'institution_name' => 'Test Charity',
    'license_number' => '12345',
    'license_certificate_path' => 'test/license.pdf',
    'email' => 'test@charity.com',
    'support_letter_path' => 'test/letter.pdf',
    'phone_number' => $phone,
    'ceo_name' => 'John Doe',
    'ceo_mobile' => '0511111111',
    'whatsapp_number' => '0511111111',
    'city' => 'Riyadh',
    'activity_type' => 'Charity',
    'project_name' => 'Community Aid',
    'project_type' => 'Aid',
    'project_file_path' => 'test/project.pdf',
    'project_manager_name' => 'Jane Doe',
    'project_manager_mobile' => '0522222222',
    'goal_1' => 'Help people',
    'beneficiaries' => 'Poor',
    'project_cost' => 100000,
    'project_outputs' => 'Happiness',
    'operational_plan_path' => 'test/plan.pdf',
    'support_scope' => 'full',
    'amount_requested' => 100000,
    'account_name' => 'Charity Acc',
    'bank_account_iban' => 'SA123456789',
    'bank_name' => 'AlRajhi',
    'bank_certificate_path' => 'test/bank.pdf',
    'status' => 'pending',
]);

echo "[+] Created Test Request: $requestNumber (Status: {$supportRequest->status})\n";

// 3. Admin Updates Status to 'waiting_for_documents'
echo "\n--- Step 1: Admin Reviews & Asks for Docs ---\n";
$controller = app(\App\Http\Controllers\API\InstitutionalSupportRequestController::class);

$updateRequest = Request::create("/api/admin/support/institutional/requests/{$supportRequest->id}/update", 'POST', [
    'status' => 'waiting_for_documents',
    'admin_response_message' => 'Please upload the receipts and report.',
]);
$updateRequest->setUserResolver(function () use ($admin) {
    return $admin;
});

$response = $controller->update($updateRequest, $supportRequest->id);
$data = json_decode($response->getContent(), true);

if ($response->status() === 200 && $data['data']['status'] === 'waiting_for_documents') {
    echo "[SUCCESS] Status changed to 'waiting_for_documents'.\n";
    echo ' > Admin Message: '.$data['data']['admin_response_message']."\n";
} else {
    echo "[FAILURE] Failed to update status.\n";
    print_r($data);
    exit;
}

// 4. Client Checks Status
echo "\n--- Step 2: Client Checks Status ---\n";
$checkRequest = Request::create('/api/support/institutional/status', 'POST', [
    'request_number' => $requestNumber,
    'phone_number' => $phone,
]);
$response = $controller->checkStatus($checkRequest);
$data = json_decode($response->getContent(), true);
echo ' > Client sees status: '.$data['status']."\n";

// 5. Client Uploads Documents (Workflow Controller)
echo "\n--- Step 3: Client Uploads Workflow Docs ---\n";
Storage::fake('public');
$fakeFile = UploadedFile::fake()->create('doc.pdf', 100);

$workflowController = app(\App\Http\Controllers\API\WorkflowController::class);
$uploadRequest = Request::create('/api/support/workflow/upload', 'POST', [
    'request_number' => $requestNumber,
    'phone_number' => $phone,
    'type' => 'institutional',
], [], [
    'closure_receipt' => $fakeFile,
    'project_report' => $fakeFile,
    'support_letter_response' => $fakeFile,
]);

$response = $workflowController->uploadDocuments($uploadRequest);
$data = json_decode($response->getContent(), true);

if ($response->status() === 200 && $data['status'] === 'documents_review') {
    echo "[SUCCESS] Documents uploaded. Status automatically changed to 'documents_review'.\n";
} else {
    echo "[FAILURE] Upload failed.\n";
    print_r($data);
    exit;
}

// 6. Verify Database State
$supportRequest->refresh();
echo " > DB Status: {$supportRequest->status}\n";
echo " > Receipt Path: {$supportRequest->closure_receipt_path}\n";

// 7. Admin Final Approval
echo "\n--- Step 4: Admin Final Approval ---\n";
$finalRequest = Request::create("/api/admin/support/institutional/requests/{$supportRequest->id}/update", 'POST', [
    'status' => 'completed',
    'admin_response_message' => 'Good job, approved.',
]);
$finalRequest->setUserResolver(function () use ($admin) {
    return $admin;
});

$response = $controller->update($finalRequest, $supportRequest->id);
$data = json_decode($response->getContent(), true);

if ($data['data']['status'] === 'completed') {
    echo "[SUCCESS] Request completed successfully.\n";
} else {
    echo "[FAILURE] Final approval failed.\n";
}

// Cleanup
$supportRequest->delete();
echo "\n--- Test Completed & Cleaned Up ---\n";
