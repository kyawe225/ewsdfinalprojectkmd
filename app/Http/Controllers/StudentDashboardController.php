<?php
namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\MeetingDetail;
use App\Models\MeetingRequest;
use App\Models\Student;
use Carbon\Carbon;
// make sure Carbon is imported for date comparisons

class StudentDashboardController extends Controller
{
    /**
     * Return all dashboard info in a single API call for the authenticated student.
     */
    public function getDashboardData()
    {
        // Get the currently authenticated student.
        $student = auth()->user();

        // Prepare the user information.
        $userInfo = [
            'name'          => $student->name,
            'email'         => $student->email,
            'last_login_at' => $student->last_login_at,
        ];

        // ---------------- VLOG STATISTICS ---------------- //
        // Retrieve vlogs uploaded by the student (author_role = "student")
        $studentVlogs = Blog::where('student_id', $student->id)
            ->where('author_role', 'student')
            ->count();

        // Retrieve vlogs uploaded by the tutor (author_role = "tutor")
        $tutorVlogs = Blog::where('student_id', $student->id)
            ->where('author_role', 'tutor')
            ->count();

        $totalBlog = $studentVlogs + $tutorVlogs;

        // ---------------- MEETING STATISTICS ---------------- //
        // Count online meetings for this student.
        $onlineMeetingsCount = MeetingDetail::with('arranging')
            ->where('meeting_type', 'online')
            ->whereHas('arranging', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->count();

        // Count campus meetings for this student.
        $campusMeetingsCount = MeetingDetail::with('arranging')
            ->where('meeting_type', 'campus')
            ->whereHas('arranging', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->count();

        $totalMeeting = $onlineMeetingsCount + $campusMeetingsCount;

        // ---------------- UPCOMING MEETINGS ---------------- //
        // Retrieve upcoming meetings by filtering on arrange_date greater than now.


        $upcomingMeetings = MeetingRequest::with('approvedArrangement')->whereHas('approvedArrangement', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })->orderByDesc("created_at")->take(10)->get();

        // ---------------- TUTOR ALLOCATION INFO ---------------- //
        // Retrieve the student's allocation with the related tutor.
        $allocation = $student->allocations()->with('tutor')->first();
        $tutorName  = $allocation && $allocation->tutor ? $allocation->tutor->name : null;
        $tutorEmail = $allocation && $allocation->tutor ? $allocation->tutor->email : null;

        // ---------------- RETURN RESPONSE ---------------- //
        return response()->json([
            'status' => 200,
            'data'   => [
                'user'              => [
                    'name'          => $userInfo['name'],
                    'email'         => $userInfo['email'],
                    'last_login_at' => $userInfo['last_login_at'],
                ],
                'tutor'             => [
                    'name'  => $tutorName,
                    'email' => $tutorEmail,
                ],
                'vlogs'             => [
                    'student'   => $studentVlogs,
                    'tutor'     => $tutorVlogs,
                    'totalBlog' => $totalBlog,
                ],
                'meetings'          => [
                    'count_online' => $onlineMeetingsCount,
                    'count_campus' => $campusMeetingsCount,
                    'totalMeeting' => $totalMeeting,
                ],
                'upcoming_meetings' => $upcomingMeetings, // new key for upcoming meetings
            ],
        ]);
    }

    /**
     * Return dashboard info for a student (for staff users).
     */
    public function getStudentDashboardData($student_id)
    {
        if (auth()->user()->hasAnyRole(['staff'])) {
            // Get the specified student.
            $student = Student::where('id', $student_id)->first();

            // Prepare the user information.
            $userInfo = [
                'name'          => $student->name,
                'email'         => $student->email,
                'last_login_at' => $student->last_login_at,
            ];

            // ---------------- VLOG STATISTICS ---------------- //
            $studentVlogs = Blog::where('student_id', $student->id)
                ->where('author_role', 'student')
                ->count();

            $tutorVlogs = Blog::where('student_id', $student->id)
                ->where('author_role', 'tutor')
                ->count();

            $totalBlog = $studentVlogs + $tutorVlogs;

            // ---------------- MEETING STATISTICS ---------------- //
            $onlineMeetingsCount = MeetingDetail::with('arranging')
                ->where('meeting_type', 'online')
                ->whereHas('arranging', function ($q) use ($student) {
                    $q->where('student_id', $student->id);
                })
                ->count();

            $campusMeetingsCount = MeetingDetail::with('arranging')
                ->where('meeting_type', 'campus')
                ->whereHas('arranging', function ($q) use ($student) {
                    $q->where('student_id', $student->id);
                })
                ->count();

            $totalMeeting = $onlineMeetingsCount + $campusMeetingsCount;

            // ---------------- UPCOMING MEETINGS ---------------- //
            $upcomingMeetings = MeetingRequest::with('approvedArrangement')->whereHas('approvedArrangement', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            })->orderByDesc("created_at")->take(10)->get();

            // ---------------- TUTOR ALLOCATION INFO ---------------- //
            $allocation = $student->allocations()->with('tutor')->first();
            $tutorName  = $allocation && $allocation->tutor ? $allocation->tutor->name : null;
            $tutorEmail = $allocation && $allocation->tutor ? $allocation->tutor->email : null;

            // ---------------- RETURN RESPONSE ---------------- //
            return response()->json([
                'status' => 200,
                'data'   => [
                    'user'              => [
                        'name'          => $userInfo['name'],
                        'email'         => $userInfo['email'],
                        'last_login_at' => $userInfo['last_login_at'],
                    ],
                    'tutor'             => [
                        'name'  => $tutorName,
                        'email' => $tutorEmail,
                    ],
                    'vlogs'             => [
                        'student'   => $studentVlogs,
                        'tutor'     => $tutorVlogs,
                        'totalBlog' => $totalBlog,
                    ],
                    'meetings'          => [
                        'count_online' => $onlineMeetingsCount,
                        'count_campus' => $campusMeetingsCount,
                        'totalMeeting' => $totalMeeting,
                    ],
                    'upcoming_meetings' => $upcomingMeetings,
                ],
            ]);
        }
    }
}
