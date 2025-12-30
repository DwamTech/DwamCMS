<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstitutionalSupportRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number',
        'institution_name',
        'license_number',
        'license_certificate_path',
        'email',
        'support_letter_path',
        'phone_number',
        'ceo_name',
        'ceo_mobile',
        'whatsapp_number',
        'city',
        'activity_type',
        'activity_type_other',
        'project_name',
        'project_type',
        'project_type_other',
        'project_file_path',
        'project_manager_name',
        'project_manager_mobile',
        'goal_1',
        'goal_2',
        'goal_3',
        'goal_4',
        'other_goals',
        'beneficiaries',
        'beneficiaries_other',
        'project_cost',
        'project_outputs',
        'operational_plan_path',
        'support_scope',
        'amount_requested',
        'account_name',
        'bank_account_iban',
        'bank_name',
        'bank_certificate_path',
        'status',
        'rejection_reason',
    ];

    protected $casts = [
        'project_cost' => 'decimal:2',
        'amount_requested' => 'decimal:2',
    ];
}
