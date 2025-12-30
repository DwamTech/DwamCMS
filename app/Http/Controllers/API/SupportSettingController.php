<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SupportSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportSettingController extends Controller
{
    // Public: Get Settings
    public function index()
    {
        $settings = SupportSetting::pluck('value', 'key');
        
        return response()->json([
            'individual_support_enabled' => $settings->get('individual_support_enabled') === 'true',
            'institutional_support_enabled' => $settings->get('institutional_support_enabled') === 'true',
        ]);
    }

    // Admin: Update Settings
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|in:individual_support_enabled,institutional_support_enabled',
            'value' => 'required|in:true,false',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        SupportSetting::updateOrCreate(
            ['key' => $request->key],
            ['value' => $request->value]
        );

        return response()->json(['message' => 'تم تحديث الإعدادات بنجاح']);
    }
}
