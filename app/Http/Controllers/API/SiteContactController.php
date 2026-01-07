<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SiteContact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteContactController extends Controller
{
    // =========================================================================
    // Guest/Public Endpoints (للعرض فقط)
    // =========================================================================

    /**
     * Get all site contact information.
     * GET /api/site-contact
     */
    public function index(): JsonResponse
    {
        $contact = SiteContact::getInstance();

        return response()->json([
            'social' => $contact->getSocialLinks(),
            'phones' => $contact->getPhones(),
            'business_details' => $contact->getBusinessDetails(),
        ]);
    }

    /**
     * Get only social media links.
     * GET /api/site-contact/social
     */
    public function social(): JsonResponse
    {
        $contact = SiteContact::getInstance();

        return response()->json($contact->getSocialLinks());
    }

    /**
     * Get only phone numbers.
     * GET /api/site-contact/phones
     */
    public function phones(): JsonResponse
    {
        $contact = SiteContact::getInstance();

        return response()->json($contact->getPhones());
    }

    /**
     * Get only business details.
     * GET /api/site-contact/business
     */
    public function business(): JsonResponse
    {
        $contact = SiteContact::getInstance();

        return response()->json($contact->getBusinessDetails());
    }

    // =========================================================================
    // Admin Endpoints (CRUD)
    // =========================================================================

    /**
     * Get all site contact information (Admin).
     * GET /api/admin/site-contact
     */
    public function show(): JsonResponse
    {
        $contact = SiteContact::getInstance();

        return response()->json([
            'id' => $contact->id,
            'social' => $contact->getSocialLinks(),
            'phones' => $contact->getPhones(),
            'business_details' => $contact->getBusinessDetails(),
            'created_at' => $contact->created_at,
            'updated_at' => $contact->updated_at,
        ]);
    }

    /**
     * Update all site contact information.
     * PUT /api/admin/site-contact
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            // Social Media
            'youtube' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'snapchat' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'tiktok' => 'nullable|url|max:255',

            // Phone Numbers
            'support_phone' => 'nullable|string|max:20',
            'management_phone' => 'nullable|string|max:20',
            'backup_phone' => 'nullable|string|max:20',

            // Business Details
            'address' => 'nullable|string|max:500',
            'commercial_register' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $contact = SiteContact::getInstance();
        $contact->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Site contact updated successfully.',
            'data' => [
                'id' => $contact->id,
                'social' => $contact->getSocialLinks(),
                'phones' => $contact->getPhones(),
                'business_details' => $contact->getBusinessDetails(),
                'updated_at' => $contact->updated_at,
            ],
        ]);
    }

    /**
     * Update only social media links.
     * PUT /api/admin/site-contact/social
     */
    public function updateSocial(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'youtube' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'snapchat' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'tiktok' => 'nullable|url|max:255',
        ]);

        $contact = SiteContact::getInstance();
        $contact->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Social media links updated successfully.',
            'data' => $contact->getSocialLinks(),
        ]);
    }

    /**
     * Update only phone numbers.
     * PUT /api/admin/site-contact/phones
     */
    public function updatePhones(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'support_phone' => 'nullable|string|max:20',
            'management_phone' => 'nullable|string|max:20',
            'backup_phone' => 'nullable|string|max:20',
        ]);

        $contact = SiteContact::getInstance();
        $contact->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Phone numbers updated successfully.',
            'data' => $contact->getPhones(),
        ]);
    }

    /**
     * Update only business details.
     * PUT /api/admin/site-contact/business
     */
    public function updateBusiness(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'address' => 'nullable|string|max:500',
            'commercial_register' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $contact = SiteContact::getInstance();
        $contact->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Business details updated successfully.',
            'data' => $contact->getBusinessDetails(),
        ]);
    }
}
