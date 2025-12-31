<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\User;
use App\Models\Book;
use App\Models\DailyVisit;
use App\Models\IndividualSupportRequest;
use App\Models\InstitutionalSupportRequest;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function summary()
    {
        $today = now()->format('Y-m-d');
        
        $totalArticles = Article::count();
        $totalUsers = User::where('role', 'user')->count();
        $totalBooks = Book::count(); // Assuming you have Book model
        
        $pendingIndividual = IndividualSupportRequest::where('status', 'pending')->count();
        $pendingInstitutional = InstitutionalSupportRequest::where('status', 'pending')->count();
        $pendingSupportRequests = $pendingIndividual + $pendingInstitutional;
        
        $todaysVisits = DailyVisit::where('visit_date', $today)->value('views_count') ?? 0;

        // Trends (Mock logic or basic calculation) - comparing to previous month
        // For simplicity, we can return static or calculated trends if we had historical data readily cached.
        // Let's assume some basic trend calculation if possible, or 0% for now.
        $trends = [
            'articles' => '+0%',
            'users' => '+0%',
            'visits' => '+0%'
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_articles' => $totalArticles,
                'total_users' => $totalUsers,
                'daily_visits' => $todaysVisits,
                'pending_support_requests' => $pendingSupportRequests,
                'total_books' => $totalBooks,
                'trends' => $trends
            ]
        ]);
    }

    public function analytics(Request $request)
    {
        $period = $request->query('period', '7d');
        $days = 7;
        if ($period == '30d') $days = 30;
        elseif ($period == '1y') $days = 365;

        $startDate = now()->subDays($days)->format('Y-m-d');

        $visits = DailyVisit::where('visit_date', '>=', $startDate)
            ->orderBy('visit_date', 'asc')
            ->get(['visit_date as date', 'views_count as visits']);

        // Ideally we merge with requests count per day too.
        // Doing a simple loop or separate query. 
        // For simplicity, mapping visits.
        
        $data = $visits->map(function($visit) {
            // Count requests for this date (expensive in loop but ok for admin dashboard 7-30 days)
            $indReq = IndividualSupportRequest::whereDate('created_at', $visit->date)->count();
            $instReq = InstitutionalSupportRequest::whereDate('created_at', $visit->date)->count();
            
            return [
                'date' => $visit->date,
                'visits' => $visit->visits,
                'requests' => $indReq + $instReq
            ];
        });

        // Fill missing dates with 0? skipped for MVP.

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function recentRequests(Request $request)
    {
        $limit = $request->query('limit', 5);

        // Fetch latest from both tables
        // To do this efficiently without union, we might just fetch $limit from both and sort in PHP
        
        $individuals = IndividualSupportRequest::latest()->take($limit)->get()->map(function($item) {
            return [
                'id' => $item->id,
                'type' => 'individual',
                'applicant_name' => $item->full_name,
                'status' => $item->status,
                'created_at' => $item->created_at
            ];
        });

        $institutions = InstitutionalSupportRequest::latest()->take($limit)->get()->map(function($item) {
            return [
                'id' => $item->id,
                'type' => 'institution',
                'applicant_name' => $item->institution_name,
                'status' => $item->status,
                'created_at' => $item->created_at
            ];
        });

        $merged = $individuals->concat($institutions)->sortByDesc('created_at')->take($limit)->values();

        return response()->json([
            'status' => 'success',
            'data' => $merged
        ]);
    }

    public function unreadNotificationsCount()
    {
        // Assuming AdminNotification model exists (created table earlier)
        // If Model not created yet, using DB
        $count = DB::table('admin_notifications')->whereNull('read_at')->count();

        return response()->json([
            'status' => 'success',
            'data' => ['count' => $count]
        ]);
    }
}
