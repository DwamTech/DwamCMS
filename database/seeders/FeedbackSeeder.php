<?php

namespace Database\Seeders;

use App\Models\Feedback;
use Illuminate\Database\Seeder;

class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        Feedback::truncate();

        // Sample suggestions (اقتراحات)
        $suggestions = [
            [
                'name' => 'أحمد محمد السيد',
                'email' => 'ahmed.sayed@example.com',
                'phone_number' => '0501234567',
                'message' => 'أقترح إضافة خاصية البحث المتقدم لتسهيل الوصول للمحتوى المطلوب بشكل أسرع.',
                'created_at' => now()->subDays(2),
            ],
            [
                'name' => 'فاطمة علي الزهراء',
                'email' => 'fatima.ali@example.com',
                'phone_number' => '0559876543',
                'message' => 'أقترح إضافة تطبيق للهواتف الذكية لسهولة الوصول للخدمات من أي مكان.',
                'created_at' => now()->subDays(5),
            ],
            [
                'name' => 'محمد عبدالله الخالدي',
                'email' => 'mohammed.abdullah@example.com',
                'phone_number' => null,
                'message' => 'سيكون من الجيد إضافة خاصية الإشعارات عبر الواتساب لمتابعة حالة الطلبات.',
                'created_at' => now()->subDays(7),
            ],
            [
                'name' => 'نورة سعد القحطاني',
                'email' => 'noura.saad@example.com',
                'phone_number' => '0541112233',
                'message' => 'أقترح إضافة قسم للأسئلة الشائعة لتقليل الاستفسارات المتكررة.',
                'created_at' => now()->subDays(10),
            ],
            [
                'name' => 'خالد إبراهيم المالكي',
                'email' => 'khalid.ibrahim@example.com',
                'phone_number' => '0533445566',
                'message' => 'أقترح إضافة خيار الدفع الإلكتروني لتسهيل عمليات التبرع والدعم.',
                'created_at' => now()->subDays(12),
            ],
            [
                'name' => 'سارة يوسف العمري',
                'email' => 'sarah.youssef@example.com',
                'phone_number' => null,
                'message' => 'يرجى إضافة ميزة حفظ المقالات المفضلة للرجوع إليها لاحقاً.',
                'created_at' => now()->subDays(15),
            ],
            [
                'name' => 'عمر حسن الشريف',
                'email' => 'omar.hassan@example.com',
                'phone_number' => '0567778899',
                'message' => 'أقترح إضافة خاصية مشاركة المحتوى على وسائل التواصل الاجتماعي مباشرة.',
                'created_at' => now()->subDays(18),
            ],
            [
                'name' => 'ريم عبدالرحمن الدوسري',
                'email' => 'reem.abdulrahman@example.com',
                'phone_number' => '0544556677',
                'message' => 'سيكون رائعاً لو أضفتم خاصية PDF للمقالات لسهولة الطباعة والمشاركة.',
                'created_at' => now()->subDays(20),
            ],
        ];

        // Sample complaints (شكاوى)
        $complaints = [
            [
                'name' => 'عبدالعزيز محمد النجار',
                'email' => 'abdulaziz.najjar@example.com',
                'phone_number' => '0512223344',
                'message' => 'واجهت صعوبة في تحميل المستندات، الموقع بطيء جداً خاصة في أوقات الذروة.',
                'created_at' => now()->subDays(1),
            ],
            [
                'name' => 'هند سالم الغامدي',
                'email' => 'hind.salem@example.com',
                'phone_number' => '0523334455',
                'message' => 'لم أستلم رسالة التأكيد على البريد الإلكتروني بعد التسجيل رغم مرور أكثر من ساعة.',
                'created_at' => now()->subDays(3),
            ],
            [
                'name' => 'ماجد فهد العتيبي',
                'email' => 'majed.fahad@example.com',
                'phone_number' => null,
                'message' => 'الموقع لا يعمل بشكل صحيح على متصفح Safari، بعض الأزرار لا تستجيب.',
                'created_at' => now()->subDays(6),
            ],
            [
                'name' => 'منى خالد الحربي',
                'email' => 'mona.khalid@example.com',
                'phone_number' => '0545556677',
                'message' => 'تم رفض طلبي دون توضيح السبب، أرجو التواصل معي لمعرفة المشكلة.',
                'created_at' => now()->subDays(8),
            ],
            [
                'name' => 'سلطان عبدالله المطيري',
                'email' => 'sultan.abdullah@example.com',
                'phone_number' => '0556667788',
                'message' => 'لا يمكنني تسجيل الدخول لحسابي رغم أن كلمة المرور صحيحة، الرجاء المساعدة.',
                'created_at' => now()->subDays(11),
            ],
            [
                'name' => 'لمى سعود الدوسري',
                'email' => 'lama.saud@example.com',
                'phone_number' => null,
                'message' => 'الصور في المكتبة لا تظهر بشكل صحيح على الهاتف المحمول.',
                'created_at' => now()->subDays(14),
            ],
        ];

        // Insert suggestions
        foreach ($suggestions as $suggestion) {
            Feedback::create([
                ...$suggestion,
                'type' => 'suggestion',
                'attachment_path' => null,
            ]);
        }

        // Insert complaints
        foreach ($complaints as $complaint) {
            Feedback::create([
                ...$complaint,
                'type' => 'complaint',
                'attachment_path' => null,
            ]);
        }

        $totalSuggestions = count($suggestions);
        $totalComplaints = count($complaints);

        $this->command->info('✅ تم إنشاء البيانات التجريبية:');
        $this->command->info("   - صندوق الاقتراحات: {$totalSuggestions} اقتراح");
        $this->command->info("   - صندوق الشكاوى: {$totalComplaints} شكوى");
    }
}
