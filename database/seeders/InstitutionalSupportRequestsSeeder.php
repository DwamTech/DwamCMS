<?php

namespace Database\Seeders;

use App\Models\InstitutionalSupportRequest;
use Illuminate\Database\Seeder;

class InstitutionalSupportRequestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 123 Requests
        InstitutionalSupportRequest::factory(123)->create();
    }
}
