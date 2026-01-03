<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\IndividualSupportRequest;
use App\Models\InstitutionalSupportRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SupportRequestController extends Controller
{
    /**
     * Get all pending support requests (Individual & Institutional).
     *
     * @return JsonResponse
     */
    public function pending(): JsonResponse
    {
        $individualRequests = IndividualSupportRequest::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $institutionalRequests = InstitutionalSupportRequest::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'individual_requests' => $individualRequests,
            'institutional_requests' => $institutionalRequests,
            'count' => $individualRequests->count() + $institutionalRequests->count(),
        ]);
    }
}
