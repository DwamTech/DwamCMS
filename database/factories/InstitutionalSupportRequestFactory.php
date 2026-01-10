<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InstitutionalSupportRequest>
 */
class InstitutionalSupportRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $order = 0;
        $order++;
        $status = fake()->randomElement(['pending', 'waiting_for_documents', 'documents_review', 'completed', 'rejected', 'archived']);
        $rejectionReason = $status === 'rejected' ? fake()->sentence() : null;

        return [
            'request_number' => str_pad($order, 4, '0', STR_PAD_LEFT),
            'institution_name' => fake()->company(),
            'license_number' => fake()->numerify('###-###-###'),
            'license_certificate_path' => 'placeholders/license.pdf',
            'email' => fake()->unique()->companyEmail(),
            'support_letter_path' => 'placeholders/letter.pdf',
            'phone_number' => fake()->phoneNumber(),
            'ceo_name' => fake()->name(),
            'ceo_mobile' => fake()->phoneNumber(),
            'whatsapp_number' => fake()->phoneNumber(),
            'city' => fake()->city(),
            'activity_type' => fake()->word(),
            'project_name' => fake()->sentence(3),
            'project_type' => fake()->word(),
            'project_file_path' => 'placeholders/project.pdf',
            'project_manager_name' => fake()->name(),
            'project_manager_mobile' => fake()->phoneNumber(),
            'goal_1' => fake()->sentence(),
            'goal_2' => fake()->sentence(),
            'beneficiaries' => fake()->sentence(),
            'project_cost' => fake()->randomFloat(2, 50000, 500000),
            'project_outputs' => fake()->paragraph(),
            'operational_plan_path' => 'placeholders/plan.pdf',
            'support_scope' => fake()->randomElement(['full', 'partial']),
            'amount_requested' => fake()->randomFloat(2, 10000, 200000),
            'account_name' => fake()->company(),
            'bank_account_iban' => 'SA'.fake()->numberBetween(1000000000, 9999999999),
            'bank_name' => fake()->company(),
            'bank_certificate_path' => 'placeholders/bank_cert.pdf',
            'status' => $status,
            'rejection_reason' => $rejectionReason,
        ];
    }
}
