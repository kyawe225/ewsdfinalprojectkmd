<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailNotification;
use App\Models\Allocation;
use App\Models\Arranging;
use App\Models\Student;
use App\Models\Tutor;
use App\Notifications\AllocatedStudent;
use App\Notifications\AllocatedTutor;
use App\ResponseModel\ResponseModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AllocationController extends Controller
{
    /**
     * Store a new allocation
     */
    public function store(Request $request)
    {
        Log::info("Creating Allocation", ['request' => $request->all()]);

        $request->validate([
            'allocation_date' => 'required|date',
            'tutor_id' => 'required|exists:tutors,id',
            'student_id' => 'required|exists:students,id',
        ]);

        $assigned_students = Allocation::where('tutor_id', $request->tutor_id)->count();

        if ($assigned_students > 30) {
            return response()->json(['error' => 'Single tutor can assign up to 30 students.'], 400);
        }

        $staff = Auth::user();

        if (!$staff) {
            Log::error("Unauthorized access attempt to create allocation");
            return response()->json(['error' => 'Unauthorized. Please login as staff.'], 401);
        }


        $existingAllocation = Allocation::where('student_id', $request->student_id)->first();
        if ($existingAllocation) {
            return response()->json(['error' => 'This student is already allocated to a tutor.'], 409);
        }


        $allocation = Allocation::create([
            'allocation_date' => $request->allocation_date,
            'allocated_by' => $staff->name,
            'staff_id' => $staff->id,
            'tutor_id' => $request->tutor_id,
            'student_id' => $request->student_id,
        ]);

        $student = Student::where("id", $allocation['student_id'])->first();
        $tutor = Tutor::where("id", $allocation['tutor_id'])->first();
        $student_job = new SendEmailNotification(new AllocatedStudent($student, $tutor), $student);
        $tutor_job = new SendEmailNotification(new AllocatedTutor($student, $tutor), $tutor);

        dispatch($student_job);
        dispatch($tutor_job);

        Log::info("Allocation Created Successfully", ['allocation' => $allocation]);

        return response()->json([
            'message' => 'Allocation created successfully!',
            'allocation' => $allocation
        ], 201);
    }


    /**
     * Get all allocations with details
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10); // Default 10 per page
        $allocations = Allocation::with(['staff', 'tutor', 'student'])->orderByDesc('created_at')->get();

        return response()->json(["data" => $allocations], 200);
    }


    /**
     * Get a single allocation with details
     */
    public function show($id)
    {
        // Ensure ID is treated as an integer
        $id = (int) $id;

        $allocation = Allocation::with(['staff', 'tutor', 'student'])->find($id);

        if (!$allocation) {
            return response()->json(['message' => 'Allocation not found'], 404);
        }

        return response()->json(['allocation' => $allocation], 200);
    }
    public function search(Request $request)
    {
        $perPage = $request->query('per_page', 10); // Default 10 per page
        $query = Allocation::with(['staff', 'tutor', 'student']);

        if ($request->has('tutor_name')) {
            $query->whereHas('tutor', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->tutor_name . '%');
            });
        }

        if ($request->has('student_name')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->student_name . '%');
            });
        }

        $allocations = $query->paginate($perPage); // Apply pagination

        return response()->json($allocations, 200);
    }

    public function ListSearch($student_id, Request $request)
    {
        $tutor_id = auth()->user()->id;
        $student = Student::where('id', $student_id)->first();

        $sample_output = Arranging::where('student_id', $student_id)->where('tutor_id', $tutor_id)->has("meetingDetails")->with(['meetingDetails', 'meetingDetails.meetingRecords'])->get()->map(function ($e, $i) {
            $temp = $e['meetingDetails']->whereIn('status', ['pending', 'completed'])->sortByDesc("created_at")->first();
            $meetingDetailCount = $e['meetingDetails']->count();
            $meetingRecord = isset($temp->meetingRecords) ? $temp['meetingRecords']->first() : null;
            return [
                "meeting_detail_id" => $temp['id'],
                "id" => $e['id'],
                "title" => $temp['topic'],
                "date" => $temp['arrange_date'],
                "time" => $temp['arrange_date'],
                "meeting_type" => $temp['meeting_type'],
                "description" => $temp['description'],
                "meeting_link" => $temp['meeting_link'],
                "meeting_app" => $temp['online_meeting_applicaiton_type'],
                "location" => $temp['location'],
                "status" => $meetingDetailCount > 1 ? "rescheduled" : ($e['status'] == "pending" ? "upcomming" : $e['status']),
                "filter_status" => Carbon::parse($temp['arrange_date'])->tz("UTC") > Carbon::now("UTC") ? "upcoming" : "pastdue",
                "meetingRecord" => $meetingRecord,
            ];
        });

        $sample_output = $sample_output->groupBy('filter_status');

        $result = [
            "id" => $student->id,
            "name" => $student->name,
            "email" => $student->email,
            "student_id" => $student->StudentID,
            "meetings" => $sample_output
        ];

        return response()->json(ResponseModel::Ok($result, '', "Meeting Fetched Successfully"));
    }

    public function ListSearchStudent(Request $request)
    {
        $student_id = auth()->user()->id;
        $student = Student::where('id', $student_id)->first();
        $tutor_id = Allocation::withTrashed()->where('student_id', $student_id)->get('tutor_id')->map(function($i) { return $i->tutor_id;});
        $sample_output = Arranging::where('student_id', $student_id)->whereIn('tutor_id', $tutor_id)->has("meetingDetails")->with(['meetingDetails'])->get()->map(function ($e, $i) {
            $temp = $e['meetingDetails']->whereIn('status', ['pending', 'cancelled', 'completed'])->sortByDesc("created_at")->first();
            $meetingDetailCount = $e['meetingDetails']->count();
            $meetingRecord = isset($temp->meetingRecords) ? $temp['meetingRecords']->first() : null;

            return [
                "meeting_detail_id" => $temp['id'],
                "id" => $e['id'],
                "title" => $temp['topic'],
                "date" => Carbon::parse($temp['arrange_date']),
                "time" => Carbon::parse($temp['arrange_date']),
                "meeting_type" => $temp['meeting_type'],
                "description" => $temp['description'],
                "meeting_link" => $temp['meeting_link'],
                "location" => $temp['location'],
                "status" => $meetingDetailCount > 1 ? "rescheduled" : ($e['status'] == "pending" ? "upcomming" : $e['status']),
                "filter_status" => Carbon::parse($temp['arrange_date'])->tz("UTC") > Carbon::now("UTC") ? "upcoming" : "pastdue",
                "meetingRecord" => $meetingRecord
            ];
        });

        $sample_output = $sample_output->groupBy('filter_status');

        $result = [
            "id" => $student->id,
            "name" => $student->name,
            "email" => $student->email,
            "student_id" => $student->StudentID,
            "meetings" => $sample_output
        ];

        return response()->json(ResponseModel::Ok($result, '', "Meeting Fetched Successfully"));
    }

    public function ListStudents()
    {
        $tutor_id = auth()->user()->id;
        $student = Allocation::where('tutor_id', $tutor_id)->get();
        $student = $student->map(function ($e) {
            return $e['student'];
        });

        return response()->json(ResponseModel::Ok($student, '', "Student related to teacher Fetched Successfully"));
    }
}
