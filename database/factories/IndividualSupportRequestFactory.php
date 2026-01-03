<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IndividualSupportRequest>
 */
class IndividualSupportRequestFactory extends Factory
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
            'full_name' => fake()->name(),
            'gender' => fake()->randomElement(['male', 'female']),
            'nationality' => 'Saudi',
            'city' => fake()->city(),
            'housing_type' => fake()->randomElement(['owned', 'rented']),
            'identity_image_path' => 'placeholders/id_card.jpg',
            'birth_date' => fake()->date(),
            'identity_expiry_date' => fake()->date('Y-m-d', '+5 years'),
            'phone_number' => fake()->phoneNumber(),
            'whatsapp_number' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'academic_qualification_path' => 'placeholders/degree.pdf',
            'scientific_activity' => fake()->jobTitle(),
            'cv_path' => 'placeholders/cv.pdf',
            'workplace' => fake()->company(),
            'support_scope' => fake()->randomElement(['full', 'partial']),
            'amount_requested' => fake()->randomFloat(2, 1000, 50000),
            'support_type' => fake()->word(),
            'has_income' => fake()->boolean(),
            'income_source' => fake()->word(),
            'marital_status' => fake()->randomElement(['single', 'married']),
            'family_members_count' => fake()->numberBetween(0, 5),
            'recommendation_path' => 'placeholders/recommendation.pdf',
            'bank_account_iban' => 'SA' . fake()->numberBetween(1000000000, 9999999999),
            'bank_name' => fake()->company(),
            'status' => $status,
            'rejection_reason' => $rejectionReason,
        ];
    }
}
