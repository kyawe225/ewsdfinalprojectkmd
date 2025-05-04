<?php

namespace App\Http\Controllers;

use App\Models\Tutor;
use App\Models\Student;
use App\Models\Blog;
use App\Models\MeetingDetail;
use App\Models\Allocation;
use App\Models\Arranging;
use App\Models\Comments;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TutorDashboardController extends Controller
{
    public function index(Request $request)
    {
        // **Get the authenticated tutor**
        $tutor = Tutor::where('id', $request->user()->id)->first();

        // Prepare the user information.
        $userInfo = [
            'name' => $tutor->name,
            'email' => $tutor->email,
            'last_login_at' => $tutor->last_login_at,
        ];

        if (!$tutor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access',
            ], 403);
        }

        // **Get filter value from request (default: "all")**
        $filter = $request->query('status', 'all');

        // **Determine Active/Inactive Students**
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $allocated_students = Allocation::where('tutor_id', $tutor->id)
            ->with(['student:id,StudentID,name,email'])
            ->get()
            ->map(function ($allocation) use ($sevenDaysAgo) {
                $student_id = $allocation->student->id;

                // Check if the student interacted in the last 7 days
                $recent_blog = Blog::where('student_id', $student_id)
                    ->where('created_at', '>=', $sevenDaysAgo)
                    ->exists();

                $recent_comment = Comments::where('student_id', $student_id)
                    ->where('created_at', '>=', $sevenDaysAgo)
                    ->exists();

                $recent_meeting = Arranging::where('student_id', $student_id)
                    ->where('updated_at', '>=', $sevenDaysAgo)
                    ->exists();

                // Assign Active/Inactive status
                $allocation->student->status = ($recent_blog || $recent_comment || $recent_meeting) ? 'Active' : 'Inactive';

                return $allocation->student;
            });

        // **Apply Filtering (if requested)**
        if ($filter === 'active') {
            $allocated_students = $allocated_students->where('status', 'Active');
        } elseif ($filter === 'inactive') {
            $allocated_students = $allocated_students->where('status', 'Inactive');
        }

        // Convert collection to array after filtering
        $allocated_students = $allocated_students->values();

        // **Count Students Under This Tutor**
        $total_students = [
            'count' => $allocated_students->count(),
            'active' => $allocated_students->where('status', 'Active')->count(),
            'inactive' => $allocated_students->where('status', 'Inactive')->count(),
        ];

        // **Fetch Blog Insights (Updated to Include "Posts by You")**
        $blogging_insights = [
            'total_posts' => Blog::where('tutor_id', $tutor->id)->count(),
            'posts_by_students' => Blog::whereNotNull('student_id')->where('tutor_id', $tutor->id)->count(),
            'posts_by_you' => Blog::whereNull('student_id')->where('tutor_id', $tutor->id)->count(), // ✅ Added "Posts by You"
            'last_blog_date' => Blog::where('tutor_id', $tutor->id)->latest()->first()?->created_at ?? null,
        ];

        // **Fetch Meetings Scheduled with This Tutor**
        $scheduled_meetings = [
            'total' => MeetingDetail::whereHas('arranging', function ($query) use ($tutor) {
                $query->where('tutor_id', $tutor->id);
            })->count(),

            'online_count' => MeetingDetail::where('meeting_type', 'online')
                ->whereHas('arranging', function ($query) use ($tutor) {
                    $query->where('tutor_id', $tutor->id);
                })->count(),

            'online_platforms' => MeetingDetail::where('meeting_type', 'online')
                ->whereHas('arranging', function ($query) use ($tutor) {
                    $query->where('tutor_id', $tutor->id);
                })->select('online_meeting_application_type')
                ->distinct()
                ->pluck('online_meeting_application_type')
                ->toArray(),

            'offline_count' => MeetingDetail::where('meeting_type', 'offline')
                ->whereHas('arranging', function ($query) use ($tutor) {
                    $query->where('tutor_id', $tutor->id);
                })->count(),

            'offline_locations' => MeetingDetail::where('meeting_type', 'offline')
                ->whereHas('arranging', function ($query) use ($tutor) {
                    $query->where('tutor_id', $tutor->id);
                })->select('location')
                ->distinct()
                ->pluck('location')
                ->toArray(),
        ];

        // **Return API Response**
        return response()->json([
            'status' => 'success',
            'message' => 'Tutor dashboard data fetched successfully',
            'data' => [
                'userInfo' => $userInfo,
                'total_students' => $total_students,
                'blogging_insights' => $blogging_insights,
                'scheduled_meetings' => $scheduled_meetings,
                'students' => $allocated_students, // Filtered student list
            ]
        ], 200);
    }

    public function tutorDashboard($tutor_id, Request $request)
    {
        if (auth()->user()->hasAnyRole(['staff'])) {
            // **Get the authenticated tutor**
            $tutor = Tutor::where('id', $tutor_id)->first();
            // Prepare the user information.
            $userInfo = [
                'name' => $tutor->name,
                'email' => $tutor->email,
                'last_login_at' => $tutor->last_login_at,
            ];

            if (!$tutor) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access',
                ], 403);
            }

            // **Get filter value from request (default: "all")**
            $filter = $request->query('status', 'all');

            // **Determine Active/Inactive Students**
            $sevenDaysAgo = Carbon::now()->subDays(7);
            $allocated_students = Allocation::where('tutor_id', $tutor->id)
                ->with(['student:id,StudentID,name,email'])
                ->get()
                ->map(function ($allocation) use ($sevenDaysAgo) {
                    $student_id = $allocation->student->id;

                    // Check if the student interacted in the last 7 days
                    $recent_blog = Blog::where('student_id', $student_id)
                        ->where('created_at', '>=', $sevenDaysAgo)
                        ->exists();

                    $recent_comment = Comments::where('student_id', $student_id)
                        ->where('created_at', '>=', $sevenDaysAgo)
                        ->exists();

                    $recent_meeting = Arranging::where('student_id', $student_id)
                        ->where('updated_at', '>=', $sevenDaysAgo)
                        ->exists();

                    // Assign Active/Inactive status
                    $allocation->student->status = ($recent_blog || $recent_comment || $recent_meeting) ? 'Active' : 'Inactive';

                    return $allocation->student;
                });

            // **Apply Filtering (if requested)**
            if ($filter === 'active') {
                $allocated_students = $allocated_students->where('status', 'Active');
            } elseif ($filter === 'inactive') {
                $allocated_students = $allocated_students->where('status', 'Inactive');
            }

            // Convert collection to array after filtering
            $allocated_students = $allocated_students->values();

            // **Count Students Under This Tutor**
            $total_students = [
                'count' => $allocated_students->count(),
                'active' => $allocated_students->where('status', 'Active')->count(),
                'inactive' => $allocated_students->where('status', 'Inactive')->count(),
            ];

            // **Fetch Blog Insights (Updated to Include "Posts by You")**
            $blogging_insights = [
                'total_posts' => Blog::where('tutor_id', $tutor->id)->count(),
                'posts_by_students' => Blog::whereNotNull('student_id')->where('tutor_id', $tutor->id)->count(),
                'posts_by_you' => Blog::whereNull('student_id')->where('tutor_id', $tutor->id)->count(), // ✅ Added "Posts by You"
                'last_blog_date' => Blog::where('tutor_id', $tutor->id)->latest()->first()?->created_at ?? null,
            ];

            // **Fetch Meetings Scheduled with This Tutor**
            $scheduled_meetings = [
                'total' => MeetingDetail::whereHas('arranging', function ($query) use ($tutor) {
                    $query->where('tutor_id', $tutor->id);
                })->count(),

                'online_count' => MeetingDetail::where('meeting_type', 'online')
                    ->whereHas('arranging', function ($query) use ($tutor) {
                        $query->where('tutor_id', $tutor->id);
                    })->count(),

                'online_platforms' => MeetingDetail::where('meeting_type', 'online')
                    ->whereHas('arranging', function ($query) use ($tutor) {
                        $query->where('tutor_id', $tutor->id);
                    })->select('online_meeting_application_type')
                    ->distinct()
                    ->pluck('online_meeting_application_type')
                    ->toArray(),

                'offline_count' => MeetingDetail::where('meeting_type', 'offline')
                    ->whereHas('arranging', function ($query) use ($tutor) {
                        $query->where('tutor_id', $tutor->id);
                    })->count(),

                'offline_locations' => MeetingDetail::where('meeting_type', 'offline')
                    ->whereHas('arranging', function ($query) use ($tutor) {
                        $query->where('tutor_id', $tutor->id);
                    })->select('location')
                    ->distinct()
                    ->pluck('location')
                    ->toArray(),
            ];

            // **Return API Response**
            return response()->json([
                'status' => 'success',
                'message' => 'Tutor dashboard data fetched successfully',
                'data' => [
                    'userInfo' => $userInfo,
                    'total_students' => $total_students,
                    'blogging_insights' => $blogging_insights,
                    'scheduled_meetings' => $scheduled_meetings,
                    'students' => $allocated_students,
                ]

            ], 200);
        }
    }
}
