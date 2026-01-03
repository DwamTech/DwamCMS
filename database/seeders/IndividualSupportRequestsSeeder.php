<?php

namespace Database\Seeders;

use App\Models\IndividualSupportRequest;
use Illuminate\Database\Seeder;

class IndividualSupportRequestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 147 Requests
        IndividualSupportRequest::factory(147)->create();
    }
}
