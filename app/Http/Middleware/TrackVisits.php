<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip if not GET request or if it's an API call to admin/analytics itself to avoid loops (though unlikely)
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        $today = now()->format('Y-m-d');
        $visitorId = $request->cookie('visitor_id');
        $lastVisitDate = $request->cookie('last_visit_date');

        // Upsert daily visit record
        $visitRecord = \App\Models\DailyVisit::firstOrCreate(
            ['visit_date' => $today],
            ['views_count' => 0, 'unique_visitors' => 0]
        );

        $isUnique = false;

        // Determine if unique visitor
        if (!$visitorId || $lastVisitDate !== $today) {
            $isUnique = true;
            $visitRecord->increment('unique_visitors');
        }

        // Always increment views
        $visitRecord->increment('views_count');

        $response = $next($request);

        // Set cookies if unique or new day
        if ($isUnique) {
             $response->withCookie(cookie()->forever('visitor_id', \Illuminate\Support\Str::uuid()));
             $response->withCookie(cookie()->forever('last_visit_date', $today));
        }

        return $response;
    }
}
