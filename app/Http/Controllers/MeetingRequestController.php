<?php

namespace App\Http\Controllers;

use App\Http\Requests\MeetingStudentRequest;
use App\Jobs\SendEmailNotification;
use App\Models\Allocation;
use App\Models\Arranging;
use App\Models\MeetingDetail;
use App\Models\MeetingRequest;
use App\Models\Student;
use App\Notifications\MeetingRequestApproveNotification;
use App\Notifications\MeetingRequestRejectStudentNotification;
use App\ResponseModel\ResponseModel;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;

class MeetingRequestController extends Controller
{
    public function index(int $arrange_id)
    {
        $allocation = Arranging::find($arrange_id)->first();
        $student_id = $allocation->student_id;
        $tutor_id = $allocation->tutor_id;
        $models = MeetingRequest::where('student_id', $student_id)->where('tutor_id', $tutor_id)->get();
        return response()->json(ResponseModel::Ok($models, "", "Fetch Successfully!"));
    }
    public function create(int $arrange_id, MeetingStudentRequest $request)
    {
        // dd(auth()->user()->roles()->get());
        // if ((!auth()->user()->hasAnyRole('student'))) {
        //     return response()->json(ResponseModel::Failed(null, "", "failed", ));
        // }
        $meeting_detail = MeetingDetail::where('arrange_id', $arrange_id)->where('status', 'pending')->first();
        $validated = $request->safe();
        $student_id = auth()->user()->id;
        $meeting_request = [
            "student_id" => $student_id,
            "arrange_date" => Carbon::parse($validated['arrange_date']),
            "tutor_id" => $validated['tutor_id'],
            "reason" => $validated['reason'],
            "topic" => $meeting_detail['topic'],
            "meeting_type" => $validated['meeting_type'],
            "location" => $validated['location'],
            "online_meeting_applicaiton" => $validated['meeting_app'],
            "approved" => false,
            "status" => "pending",
            "approved_arrange_id" => $arrange_id
        ];
        DB::beginTransaction();
        try {
            MeetingRequest::where('approved_arrange_id', $arrange_id)->where('status', 'pending')->update([
                'status' => 'cancelled',
                'updated_at' => Carbon::now("UTC")
            ]);
            $model = MeetingRequest::create(attributes: $meeting_request);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            dd($e);
        }

        return response()->json(ResponseModel::Ok($model, $model->id, "Requested successfully"));
    }

    public function cancelRequest(int $id)
    {
        if ((!auth()->user()->hasAnyRole('student'))) {
            return response()->json(ResponseModel::Failed(null, "", "failed"));
        }
        $model = MeetingRequest::where('id', $id)->first();
        $model->status = "cancelled";
        $model->updated_at = Carbon::now('UTC');
        $model->save();
        return response()->json(ResponseModel::Ok($model, $model->id, "Cancelled Successfully"));
    }

    public function rejectRequest(int $id)
    {
        if ((!auth()->user()->hasAnyRole('tutor'))) {
            return response()->json(ResponseModel::Failed(null, "", "failed"));
        }
        $validated_data = request()->validate([
            'reject_reason' => 'nullable|string'
        ]);
        $sample = [];
        $model = MeetingRequest::where('id', $id)->first();
        $arranging = Arranging::where('id', $model->approved_arrange_id)->with(['student', 'tutor'])->first();
        $sample['status'] = "reject";
        $sample['approved_reject_date'] = Carbon::now('UTC');
        $sample['rejcet_reason'] = array_key_exists('reject_reason', $validated_data) ? $validated_data['reject_reason'] : "";
        $sample['updated_at'] = Carbon::now('UTC');
        $model->update($sample);

        $student_job = new SendEmailNotification(new MeetingRequestRejectStudentNotification($arranging->student, $arranging->tutor), $arranging->student);

        dispatch($student_job)->afterResponse();

        return response()->json(ResponseModel::Ok($model, $model->id, "Rejected Successfully"));
    }

    public function approveRequest(int $id)
    {
        if ((!auth()->user()->hasAnyRole('tutor'))) {
            return response()->json(ResponseModel::Failed(null, "", "failed"));
        }

        $validated_data = request()->validate([
            'link' => 'nullable|string',
            'description' => 'nullable|string'
        ]);

        $model = MeetingRequest::where('id', $id)->first();
        $arranging = Arranging::where('id', $model->approved_arrange_id)->with(['student', 'tutor'])->first();
        $sample['status'] = "approved";
        $sample['approved_reject_date'] = Carbon::now('UTC');
        $sample['rejcet_reason'] = "";
        $sample['updated_at'] = Carbon::now('UTC');
        $oldMeetingDetail = MeetingDetail::where("arrange_id", $model->approved_arrange_id)->where("status", 'pending')->first();
        $meetingDetail = [
            "arrange_date" => $model->arrange_date,
            "meeting_type" => $model->meeting_type,
            "topic" => $model->topic,
            "location" => $model->location,
            "online_meeting_applicaiton" => $model->online_meeting_application,
            "arrange_id" => $model->approved_arrange_id,
            "status" => "pending",
            "meeting_link" => $oldMeetingDetail->meeting_link,
            "description" => $oldMeetingDetail->description
        ];
        DB::beginTransaction();
        try {
            $oldMeetingDetail->update([
                "status" => "rescheduled"
            ]);
            $meetingDetail = MeetingDetail::create($meetingDetail);
            $model->update($sample);
            DB::commit();
            $student_job = new SendEmailNotification(new MeetingRequestApproveNotification($arranging->student, $arranging->tutor), $arranging->student);
            dispatch($student_job)->afterResponse();
        } catch (Exception $e) {
            DB::rollBack();
        }

        return response()->json(ResponseModel::Ok($meetingDetail, $model->id, "Approved Successfully"));
    }

    public function meetingRequestList()
    {
        $tutor_id = auth()->user()->id;

        $sample_output = Arranging::where('tutor_id', $tutor_id)->whereHas('meetingRequests', function ($query) {
            $query->whereIn('status', ['pending']);
        })->with(['meetingDetails', 'meetingRequests', 'student'])->where('status', 'pending')->get();

        $sample_output = $sample_output->map(function ($e, $i) {
            $temp = $e['meetingDetails']->whereIn('status', ['pending'])->sortByDesc("created_at")->first();
            $tempRequest = $e['meetingRequests']->whereIn('status', ['pending'])->sortByDesc("created_at")->first();
            if ($tempRequest != null) {
                $meetingDetailCount = $e['meetingDetails']->count();
                return [
                    "arrange_id" => $e['id'],
                    "id" => $tempRequest['id'],
                    "title" => $temp['topic'],
                    "student_name" => $e->student['name'],
                    "email" => $e->student['email'],
                    "org_date" => Carbon::parse($temp['arrange_date']),
                    "org_time" => Carbon::parse($temp['arrange_date']),
                    "org_meeting_type" => $temp['meeting_type'],
                    "org_meeting_app" => $temp['online_meeting_application_type'],
                    "org_meeting_link" => $temp['meeting_link'],
                    "org_location" => $temp['location'],
                    "new_date" => Carbon::parse($tempRequest['arrange_date']),
                    "new_time" => Carbon::parse($tempRequest['arrange_date']),
                    "new_meeting_type" => $tempRequest['meeting_type'],
                    "new_meeting_app" => $tempRequest['online_meeting_applicaiton'],
                    "new_location" => $tempRequest['location'],
                    "reason" => $tempRequest['reason'],
                    "status" => $meetingDetailCount > 1 ? "rescheduled" : ($e['status'] == "pending" ? "upcomming" : $e['status']),
                    "filter_status" => Carbon::parse($temp['arrange_date'])->tz("UTC") > Carbon::now("UTC") ? "upcoming" : "pastdue"
                ];
            }
        });
        return response()->json(ResponseModel::Ok($sample_output, '', "Meeting Request Successfully fetched"));
    }

}
