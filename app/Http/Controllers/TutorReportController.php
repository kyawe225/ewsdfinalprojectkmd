<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use App\Models\Student;
use App\Models\Allocation;

class TutorReportController extends Controller
{
    public function report(Request $request)
    {
        

        $user = Auth::guard('sanctum')->user();

        if (!($user instanceof \App\Models\Tutor)) {
            return response()->json(['error' => 'Unauthorized. Only tutors can access this report.'], 403);
        }

        $tutor = $user;
        $search = $request->input('search');
        $minDays = $request->input('min_days', 0);
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        // Get students allocated to this tutor
        $studentQuery = Student::whereIn('id', function ($query) use ($tutor) {
            $query->select('student_id')
                  ->from('allocations')
                  ->where('tutor_id', $tutor->id);
        })
        // No meetings with this tutor
        ->whereDoesntHave('arrangings', function ($q) use ($tutor) {
            $q->where('tutor_id', $tutor->id);
        })
        // No comments
        ->whereDoesntHave('comments')
        // No blog posts
        ->whereDoesntHave('blogs');

        // Search by name or student ID
        if ($search) {
            $studentQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('StudentID', 'like', "%$search%");
            });
        }

        // Fetch and calculate inactive days
        $students = $studentQuery->get()->map(function ($student) {
            $lastActive = $student->last_login_at
                ? Carbon::parse($student->last_login_at)
                : Carbon::parse($student->created_at);

            return [
                'student_id'     => $student->StudentID,
                'name'           => $student->name,
                'email'          => $student->email,
                'last_active'    => $lastActive->toDateString(),
                'inactive_days'  => round($lastActive->diffInDays(Carbon::now())),
            ];
        })->filter(function ($s) use ($minDays) {
            return $s['inactive_days'] >= $minDays;
        })->sortByDesc('inactive_days')->values();

        // Paginate manually
        $pagedStudents = new LengthAwarePaginator(
            $students->forPage($page, $perPage),
            $students->count(),
            $perPage,
            $page,
            ['path' => url()->current(), 'query' => $request->query()]
        );

        // Message count (based on meetings) for last 7 days
        $messageStats = collect(range(6, 0))->mapWithKeys(function ($i) use ($tutor) {
            $day = Carbon::now()->subDays($i)->format('l');
            $count = DB::table('meeting_detail')
                ->whereIn('arrange_id', function ($q) use ($tutor) {
                    $q->select('id')
                      ->from('arrangings')
                      ->where('tutor_id', $tutor->id);
                })
                ->whereDate('arrange_date', Carbon::now()->subDays($i)->toDateString())
                ->count();
            return [$day => $count];
        });

        return response()->json([
            'data' => [
                'messages_last_7_days' => $messageStats,
                'inactive_students' => $pagedStudents->items(),
            ],
            'meta' => [
                'current_page' => $pagedStudents->currentPage(),
                'per_page' => $pagedStudents->perPage(),
                'total' => $pagedStudents->total(),
                'last_page' => $pagedStudents->lastPage(),
            ]
        ]);
    }
}
