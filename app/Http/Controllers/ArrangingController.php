<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailNotification;
use App\Mail\MeetingCancelMail;
use App\Mail\MeetingCreateMail;
use App\Models\Arranging;
use App\Models\Student;
use App\Models\Tutor;
use App\Notifications\AllocatedStudent;
use App\Notifications\MeetingCancelNotification;
use App\Notifications\MeetingCancelStudentNotification;
use App\Notifications\MeetingCreateNotification;
use App\Notifications\MeetingCreateStudentNotification;
use Illuminate\Http\Request;
use App\Models\MeetingDetail;
use Illuminate\Support\Facades\Auth;

class ArrangingController extends Controller
{
    /**
     * Fetch all meeting arrangements.
     */
    public function index(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated.'], 401);
        }

        $perPage = $request->query('per_page', 10); // Default 10 per page

        // Fetch Arrangements with related Student, Tutor, and Meeting Details
        $meetings = Arranging::with([
            'student',
            'tutor',
            'meetingDetails'
        ])
            ->when($user instanceof \App\Models\Student, function ($query) use ($user) {
                return $query->where('student_id', $user->id);
            })
            ->when($user instanceof \App\Models\Tutor, function ($query) use ($user) {
                return $query->where('tutor_id', $user->id);
            })
            ->whereIn('status', ['pending', 'completed']) // Filter only these statuses
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json([
            "data" => $meetings,
            "meta" => [
                "current_page" => $meetings->currentPage(),
                "total_pages" => $meetings->lastPage(),
                "total_items" => $meetings->total(),
            ]
        ], 200);
    }

    /**
     * Store a new meeting arrangement.
     */
    public function store(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user || !$user instanceof \App\Models\Tutor) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'meeting_type' => 'required|in:online,offline',
            'arrange_date' => 'required|date',
            'location' => 'nullable|string',
            'meeting_link' => 'nullable|string',
            'meeting_app' => 'nullable|string',
            'topic' => 'required|string',
            'description' => 'nullable|string',
        ]);

        // Check if an arrangement already exists for the student and tutor
        // $existingArrangement = Arranging::where('tutor_id', $user->id)
        //     ->where('student_id', $validated['student_id'])
        //     ->where('status', 'pending')
        //     ->exists();

        // dd($existingArrangement);

        // if ($existingArrangement) {
        //     return response()->json(['message' => 'An active arrangement already exists with this student.'], 409);
        // }

        // Create Arranging Entry
        $arranging = Arranging::create([
            'tutor_id' => $user->id,
            'student_id' => $validated['student_id'],
            'status' => 'pending',
        ]);

        // Create MeetingDetail Entry
        $meetingDetail = MeetingDetail::create([
            'arrange_id' => $arranging->id,
            'arrange_date' => $validated['arrange_date'],
            'meeting_type' => $validated['meeting_type'],
            'location' => $validated['location'],
            'meeting_link' => $validated['meeting_link'],
            'online_meeting_application_type' => $validated['meeting_app'],
            'topic' => $validated['topic'],
            'description' => $validated['description'],
            'status' => 'pending',
        ]);

        $student = Student::where("id", $arranging['student_id'])->first();
        $tutor = Tutor::where("id", $arranging['tutor_id'])->first();
        $student_job = new SendEmailNotification(new MeetingCreateStudentNotification($student, $tutor), $student);
        $tutor_job = new SendEmailNotification(new MeetingCreateNotification($student, $tutor), $tutor);

        dispatch($student_job);
        dispatch($tutor_job);

        return response()->json([
            'message' => 'Meeting scheduled successfully!',
            'arranging' => $arranging,
            'meeting_detail' => $meetingDetail
        ], 201);
    }

    /**
     * Show a specific meeting arrangement.
     */
    public function show($id)
    {
        $arranging = Arranging::with(['student', 'tutor', 'meetingDetails'])->find($id);

        if (!$arranging) {
            return response()->json(['message' => 'Arrangement not found'], 404);
        }

        return response()->json($arranging, 200);
    }

    /**
     * Update a meeting arrangement's status.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::guard('sanctum')->user();
        $arranging = Arranging::find($id);

        if (!$arranging) {
            return response()->json(['message' => 'Arrangement not found'], 404);
        }

        if ($user->id !== $arranging->tutor_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'in:pending,completed,canceled',
            'arrange_date' => 'nullable|date',
            'meeting_type' => 'nullable|in:online,offline',
            'location' => 'nullable|string',
            'meeting_link' => 'nullable|string',
            'meeting_app' => 'nullable|string',
            'topic' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        // Update arrangings.status (if provided)
        if (isset($validated['status'])) {
            $arranging->update(['status' => $validated['status']]);
        }

        // Update meeting detail
        $meetingDetail = MeetingDetail::where('arrange_id', $id)->first();

        if ($meetingDetail) {
            $meetingDetail->update([
                'arrange_date' => $validated['arrange_date'] ?? $meetingDetail->arrange_date,
                'meeting_type' => $validated['meeting_type'] ?? $meetingDetail->meeting_type,
                'location' => $validated['location'] ?? $meetingDetail->location,
                'meeting_link' => $validated['meeting_link'] ?? $meetingDetail->meeting_link,
                'online_meeting_application_type' => $validated['meeting_app'] ?? $meetingDetail->online_meeting_application_type,
                'topic' => $validated['topic'] ?? $meetingDetail->topic,
                'description' => $validated['description'] ?? $meetingDetail->description,
                'status' => $validated['status'] ?? $meetingDetail->status,
            ]);
        }

        return response()->json([
            'message' => 'Meeting updated successfully!',
            'arranging' => $arranging,
            'meeting_detail' => $meetingDetail
        ], 200);
    }


    /**
     * Delete a meeting arrangement.
     */
    public function destroy($id)
    {
        $arranging = Arranging::find($id);

        if (!$arranging) {
            return response()->json(['message' => 'Arrangement not found'], 404);
        }

        // Ensure related meeting details are also deleted
        MeetingDetail::where('arrange_id', $id)->delete();
        $arranging->delete();

        $student = Student::where("id", $arranging['student_id'])->first();
        $tutor = Tutor::where("id", $arranging['tutor_id'])->first();
        $student_job = new SendEmailNotification(new MeetingCancelStudentNotification($student, $tutor), $student);
        $tutor_job = new SendEmailNotification(new MeetingCancelNotification($student, $tutor), $tutor);

        dispatch($student_job);
        dispatch($tutor_job);

        return response()->json(['message' => 'Arrangement deleted successfully'], 200);
    }
}
