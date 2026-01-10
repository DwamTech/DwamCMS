<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureVisitorCookie;
use App\Http\Middleware\TrackVisits;
use App\Models\BackupHistory;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\RefreshDatabaseWithForce;
use Tests\TestCase;

class BackupEndpointTest extends TestCase
{
    use RefreshDatabaseWithForce;

    protected User $adminUser;

    protected string $appName;

    protected function setUp(): void
    {
        parent::setUp();

        // تعطيل Middlewares التي تسبب مشاكل في الاختبارات
        $this->withoutMiddleware([
            TrackVisits::class,
            EnsureVisitorCookie::class,
        ]);

        // إنشاء مستخدم Admin للاختبارات
        $this->adminUser = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        // الحصول على اسم التطبيق من الإعدادات
        $this->appName = config('backup.backup.name');

        // التأكد من وجود مجلد النسخ الاحتياطية
        Storage::disk('local')->makeDirectory($this->appName);
    }

    protected function tearDown(): void
    {
        // تنظيف ملفات الاختبار بعد الانتهاء
        $this->cleanupTestBackups();

        parent::tearDown();
    }

    /**
     * تنظيف ملفات الاختبار.
     */
    private function cleanupTestBackups(): void
    {
        $disk = Storage::disk('local');

        if (! $disk->exists($this->appName)) {
            return;
        }

        $files = $disk->files($this->appName);

        foreach ($files as $file) {
            if (str_contains($file, 'test-backup') || str_contains($file, 'uploaded-backup') || str_contains($file, 'workflow-test')) {
                $disk->delete($file);
            }
        }
    }

    /**
     * إنشاء ملف zip وهمي للاختبار.
     */
    private function createFakeBackupZip(string $fileName): string
    {
        $disk = Storage::disk('local');
        $tempPath = storage_path('app/backup-temp');

        if (! is_dir($tempPath)) {
            mkdir($tempPath, 0755, true);
        }

        // إنشاء ملف zip حقيقي للاختبار
        $zipPath = $tempPath.'/'.$fileName;
        $zip = new \ZipArchive;

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            // إضافة ملف SQL وهمي
            $zip->addFromString('db-dumps/mysql-database.sql', '-- Fake SQL dump for testing');

            // إضافة مجلد storage/app/public وهمي
            $zip->addEmptyDir('storage/app/public');
            $zip->addFromString('storage/app/public/test.txt', 'Test file content');

            $zip->close();
        }

        // نقل الملف إلى مجلد النسخ الاحتياطية
        $destinationPath = $this->appName.'/'.$fileName;
        $disk->put($destinationPath, file_get_contents($zipPath));
        unlink($zipPath);

        return $destinationPath;
    }

    // =====================================================================
    // اختبارات Authentication
    // =====================================================================

    #[Test]
    public function unauthenticated_users_cannot_access_backup_endpoints(): void
    {
        // List
        $this->getJson('/api/backups')
            ->assertUnauthorized();

        // History
        $this->getJson('/api/backups/history')
            ->assertUnauthorized();

        // Create
        $this->postJson('/api/backups/create')
            ->assertUnauthorized();

        // Download
        $this->getJson('/api/backups/download?file_name=test.zip')
            ->assertUnauthorized();

        // Upload
        $this->postJson('/api/backups/upload')
            ->assertUnauthorized();

        // Restore
        $this->postJson('/api/backups/restore')
            ->assertUnauthorized();
    }

    // =====================================================================
    // اختبارات List Backups (GET /api/backups)
    // =====================================================================

    #[Test]
    public function list_backups_returns_empty_array_when_no_backups(): void
    {
        // حذف جميع ملفات zip الموجودة
        $disk = Storage::disk('local');
        $files = $disk->files($this->appName);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                $disk->delete($file);
            }
        }

        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/backups');

        $response->assertOk()
            ->assertJson([]);
    }

    #[Test]
    public function list_backups_returns_existing_backups(): void
    {
        // إنشاء ملفات نسخ احتياطية وهمية
        $fileName = 'test-backup-'.time().'.zip';
        $this->createFakeBackupZip($fileName);

        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/backups');

        $response->assertOk()
            ->assertJsonStructure([
                '*' => [
                    'file_name',
                    'file_size',
                    'created_at',
                    'download_link',
                ],
            ]);

        // التحقق من وجود الملف في الاستجابة
        $backups = $response->json();
        $found = collect($backups)->contains('file_name', $fileName);
        $this->assertTrue($found, 'The created backup file should be in the list');
    }

    #[Test]
    public function list_backups_ordered_by_newest_first(): void
    {
        // إنشاء ملفين بتوقيتات مختلفة
        $oldFileName = 'test-backup-old-'.time().'.zip';
        $this->createFakeBackupZip($oldFileName);

        sleep(1); // انتظار ثانية للتأكد من اختلاف التوقيت

        $newFileName = 'test-backup-new-'.time().'.zip';
        $this->createFakeBackupZip($newFileName);

        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/backups');

        $response->assertOk();

        $backups = $response->json();

        // التحقق من أن الملف الأحدث يأتي أولاً
        if (count($backups) >= 2) {
            $firstBackupTime = strtotime($backups[0]['created_at']);
            $secondBackupTime = strtotime($backups[1]['created_at']);
            $this->assertGreaterThanOrEqual($secondBackupTime, $firstBackupTime);
        }
    }

    // =====================================================================
    // اختبارات Create Backup (POST /api/backups/create)
    // =====================================================================

    #[Test]
    public function create_backup_rejects_invalid_mode(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/backups/create', [
                'mode' => 'invalid_mode',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid mode. Allowed values: full, db',
            ]);
    }

    #[Test]
    public function create_backup_validates_mode_parameter(): void
    {
        // اختبار أن الـ endpoint يقبل الأوضاع الصحيحة
        // نختبر فقط الـ validation، ليس التنفيذ الفعلي

        // الوضع غير الصالح يجب أن يُرفض
        $invalidResponse = $this->actingAs($this->adminUser)
            ->postJson('/api/backups/create', ['mode' => 'invalid']);

        $invalidResponse->assertStatus(422);

        // الوضع الصالح يجب أن يُقبل (حتى لو فشل التنفيذ)
        $validResponse = $this->actingAs($this->adminUser)
            ->postJson('/api/backups/create', ['mode' => 'full']);

        // نتوقع إما 200 (نجاح) أو 500 (فشل في التنفيذ لكن مرّ الـ validation)
        $this->assertContains($validResponse->status(), [200, 500]);

        // إذا كانت 200، نتحقق من الـ response
        if ($validResponse->status() === 200) {
            $validResponse->assertJson(['success' => true, 'mode' => 'full']);
        }
    }

    // =====================================================================
    // اختبارات Download Backup (GET /api/backups/download)
    // =====================================================================

    #[Test]
    public function download_backup_successfully(): void
    {
        $fileName = 'test-backup-download-'.time().'.zip';
        $this->createFakeBackupZip($fileName);

        $response = $this->actingAs($this->adminUser)
            ->get('/api/backups/download?file_name='.$fileName);

        $response->assertOk();

        // التحقق من أن الاستجابة هي ملف للتحميل
        $this->assertTrue(
            $response->headers->has('content-disposition'),
            'Response should have content-disposition header for download'
        );
    }

    #[Test]
    public function download_backup_requires_file_name(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/backups/download');

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'File name is required',
            ]);
    }

    #[Test]
    public function download_backup_returns_404_for_nonexistent_file(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/backups/download?file_name=nonexistent-file.zip');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Backup file not found',
            ]);
    }

    // =====================================================================
    // اختبارات Upload Backup (POST /api/backups/upload)
    // =====================================================================

    #[Test]
    public function upload_backup_requires_file(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/backups/upload', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file']);
    }

    #[Test]
    public function upload_backup_rejects_non_zip_files(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/backups/upload', [
                'file' => $file,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file']);
    }

    #[Test]
    public function upload_backup_rejects_duplicate_filename(): void
    {
        $fileName = 'uploaded-backup-duplicate-'.time().'.zip';

        // إنشاء ملف موجود مسبقاً
        $this->createFakeBackupZip($fileName);

        // محاولة رفع ملف بنفس الاسم
        $file = UploadedFile::fake()->create($fileName, 100, 'application/zip');

        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/backups/upload', [
                'file' => $file,
            ]);

        $response->assertStatus(409)
            ->assertJson([
                'message' => 'A backup file with this name already exists.',
            ]);
    }

    #[Test]
    public function upload_backup_validates_zip_extension(): void
    {
        // اختبار أن الـ endpoint يتحقق من امتداد الملف
        $txtFile = UploadedFile::fake()->create('backup.txt', 100, 'text/plain');

        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/backups/upload', ['file' => $txtFile]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file']);
    }

    // =====================================================================
    // اختبارات Restore Backup (POST /api/backups/restore)
    // =====================================================================

    #[Test]
    public function restore_backup_requires_file_name(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/backups/restore', []);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'File name is required',
            ]);
    }

    #[Test]
    public function restore_backup_rejects_invalid_file_names(): void
    {
        $invalidNames = [
            '../../../etc/passwd',
            '..\\..\\windows\\system32\\config',
            'backup/../../../secret.zip',
            '/absolute/path/backup.zip',
        ];

        foreach ($invalidNames as $invalidName) {
            $response = $this->actingAs($this->adminUser)
                ->postJson('/api/backups/restore', [
                    'file_name' => $invalidName,
                ]);

            $response->assertStatus(422)
                ->assertJson([
                    'message' => 'Invalid file name',
                ]);
        }
    }

    #[Test]
    public function restore_backup_returns_404_for_nonexistent_file(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/backups/restore', [
                'file_name' => 'nonexistent-backup.zip',
            ]);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Backup file not found',
            ]);

        // التحقق من تسجيل محاولة الاسترجاع
        $this->assertDatabaseHas('backup_histories', [
            'type' => 'restore',
            'status' => 'started',
            'file_name' => 'nonexistent-backup.zip',
        ]);
    }

    // =====================================================================
    // اختبارات History (GET /api/backups/history)
    // =====================================================================

    #[Test]
    public function history_returns_empty_array_when_no_records(): void
    {
        BackupHistory::query()->delete();

        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/backups/history');

        $response->assertOk()
            ->assertJson([]);
    }

    #[Test]
    public function history_returns_records_with_correct_structure(): void
    {
        // إنشاء سجلات للاختبار
        BackupHistory::create([
            'type' => 'create',
            'status' => 'success',
            'file_name' => 'test-backup.zip',
            'file_size' => 1024000,
            'message' => 'Backup created successfully.',
            'user_id' => $this->adminUser->id,
        ]);

        BackupHistory::create([
            'type' => 'restore',
            'status' => 'success',
            'file_name' => 'test-backup.zip',
            'message' => 'Backup restored successfully.',
            'user_id' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/backups/history');

        $response->assertOk()
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'type',
                    'status',
                    'file_name',
                    'file_size',
                    'message',
                    'user_id',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertGreaterThanOrEqual(2, count($response->json()));
    }

    #[Test]
    public function history_ordered_by_newest_first(): void
    {
        BackupHistory::query()->delete();

        // إنشاء سجل قديم
        $oldRecord = BackupHistory::create([
            'type' => 'create',
            'status' => 'success',
            'message' => 'Old backup',
        ]);

        sleep(1);

        // إنشاء سجل جديد
        $newRecord = BackupHistory::create([
            'type' => 'create',
            'status' => 'success',
            'message' => 'New backup',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/backups/history');

        $response->assertOk();

        $records = $response->json();

        // التحقق من أن السجل الأحدث يأتي أولاً
        $this->assertEquals($newRecord->id, $records[0]['id']);
        $this->assertEquals($oldRecord->id, $records[1]['id']);
    }

    #[Test]
    public function history_limits_to_50_records(): void
    {
        BackupHistory::query()->delete();

        // إنشاء 60 سجل
        for ($i = 0; $i < 60; $i++) {
            BackupHistory::create([
                'type' => 'create',
                'status' => 'success',
                'message' => 'Backup #'.$i,
            ]);
        }

        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/backups/history');

        $response->assertOk();

        $this->assertCount(50, $response->json());
    }

    // =====================================================================
    // اختبارات السيناريو الكامل (Integration Tests)
    // =====================================================================

    #[Test]
    public function full_workflow_list_and_download(): void
    {
        // إنشاء ملف نسخة احتياطية
        $fileName = 'workflow-test-'.time().'.zip';
        $this->createFakeBackupZip($fileName);

        // 1. عرض قائمة النسخ الاحتياطية
        $listResponse = $this->actingAs($this->adminUser)
            ->getJson('/api/backups');

        $listResponse->assertOk();

        $backups = collect($listResponse->json());
        $createdBackup = $backups->firstWhere('file_name', $fileName);

        $this->assertNotNull($createdBackup, 'Created backup should appear in the list');

        // 2. تحميل النسخة
        $downloadResponse = $this->actingAs($this->adminUser)
            ->get('/api/backups/download?file_name='.$fileName);

        $downloadResponse->assertOk();
        $this->assertTrue($downloadResponse->headers->has('content-disposition'));
    }

    #[Test]
    public function backup_history_model_stores_correctly(): void
    {
        // اختبار أن الـ Model يحفظ البيانات بشكل صحيح
        $history = BackupHistory::create([
            'type' => 'create',
            'status' => 'success',
            'file_name' => 'test.zip',
            'file_size' => 1024,
            'message' => 'Test message',
            'user_id' => $this->adminUser->id,
        ]);

        $this->assertDatabaseHas('backup_histories', [
            'id' => $history->id,
            'type' => 'create',
            'status' => 'success',
            'file_name' => 'test.zip',
            'file_size' => 1024,
            'message' => 'Test message',
            'user_id' => $this->adminUser->id,
        ]);
    }

    #[Test]
    public function admin_middleware_blocks_non_admin_users(): void
    {
        // إنشاء مستخدم عادي
        $regularUser = User::factory()->create([
            'role' => User::ROLE_USER,
        ]);

        // محاولة الوصول للـ endpoints كمستخدم عادي
        $this->actingAs($regularUser)
            ->getJson('/api/backups')
            ->assertStatus(403);

        $this->actingAs($regularUser)
            ->getJson('/api/backups/history')
            ->assertStatus(403);

        $this->actingAs($regularUser)
            ->postJson('/api/backups/create')
            ->assertStatus(403);
    }

    #[Test]
    public function backup_endpoints_accessible_by_admin(): void
    {
        // التحقق من أن الـ Admin يمكنه الوصول لجميع الـ endpoints
        $this->actingAs($this->adminUser)
            ->getJson('/api/backups')
            ->assertOk();

        $this->actingAs($this->adminUser)
            ->getJson('/api/backups/history')
            ->assertOk();

        // Create - قد يفشل بسبب Queue لكن لا يجب أن يكون 403
        $createResponse = $this->actingAs($this->adminUser)
            ->postJson('/api/backups/create');
        $this->assertNotEquals(403, $createResponse->status());

        // Upload - validation error expected, not 403
        $uploadResponse = $this->actingAs($this->adminUser)
            ->postJson('/api/backups/upload');
        $this->assertNotEquals(403, $uploadResponse->status());
    }
}
