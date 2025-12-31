<?php

namespace App\Http\Middleware;

use App\Models\SupportSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $settingKey = "module_{$module}_enabled";
        $isEnabled = SupportSetting::where('key', $settingKey)->first();

        // If setting doesn't exist, we assume it's enabled by default (for safety)
        if ($isEnabled && $isEnabled->value === 'false') {
            $messages = [
                'articles' => 'عفواً، قسم المقالات مغلق حالياً',
                'audios' => 'عفواً، قسم الصوتيات مغلق حالياً',
                'visuals' => 'عفواً، قسم المرئيات مغلق حالياً',
                'galleries' => 'عفواً، قسم الجاليري مغلق حالياً',
                'library' => 'عفواً، المكتبة مغلقة حالياً',
                'links' => 'عفواً، قسم الروابط مغلق حالياً',
            ];

            $message = $messages[$module] ?? 'هذا القسم مغلق حالياً';

            return response()->json([
                'status' => 'error',
                'message' => $message,
                'module' => $module,
                'enabled' => false
            ], 403);
        }

        return $next($request);
    }
}
