<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndividualSupportRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number',
        'full_name',
        'gender',
        'nationality',
        'city',
        'housing_type',
        'housing_type_other',
        'identity_image_path',
        'birth_date',
        'identity_expiry_date',
        'phone_number',
        'whatsapp_number',
        'email',
        'academic_qualification_path',
        'scientific_activity',
        'scientific_activity_other',
        'cv_path',
        'workplace',
        'support_scope',
        'amount_requested',
        'support_type',
        'support_type_other',
        'has_income',
        'income_source',
        'marital_status',
        'family_members_count',
        'recommendation_path',
        'bank_account_iban',
        'bank_name',
        'status',
        'rejection_reason',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'identity_expiry_date' => 'date',
        'has_income' => 'boolean',
        'amount_requested' => 'decimal:2',
    ];
}
