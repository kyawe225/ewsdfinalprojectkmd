<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkAllocationRequest;
use App\Jobs\SendEmailNotification;
use App\Models\Allocation;
use App\Models\Student;
use App\Models\Tutor;
use App\Notifications\AllocatedStudent;
use App\Notifications\AllocatedTutor;
use App\ResponseModel\ResponseModel;
use Carbon\Carbon;
use DB;
use Exception;
use Log;

class BulkAllocationController extends Controller
{
    public function allocate(BulkAllocationRequest $request)
    {
        $requested_vars = $request->safe();


        $allocates = [];
        foreach ($requested_vars->student_ids as $i) {
            $assigned_student = Allocation::where('student_id', $i)->exists();
            if ($assigned_student) {
                return response()->json(ResponseModel::Failed(false, "", "There's contains already assigned student."));
            }
            $tmpAllocate = [
                'allocation_date' => Carbon::now('utc'),
                'allocated_by' => auth()->user()->name,
                'staff_id' => auth()->user()->id,
                'tutor_id' => $requested_vars['tutor_id'],
                'student_id' => $i
            ];
            array_push($allocates, $tmpAllocate);
        }
        DB::beginTransaction();
        try {
            $assigned_students = Allocation::where('tutor_id',$request->tutor_id)->count();

            if(($assigned_students + count($allocates)) > 30){
                throw new Exception("Single tutor can assign up to 30 students. Current teacher has $assigned_student students.");
            }

            foreach ($allocates as $i) {
                Allocation::create($i);
                // send notification
                $student = Student::where("id", $i['student_id'])->first();
                $tutor = Tutor::where("id", $i['tutor_id'])->first();
                $student_job = new SendEmailNotification(new AllocatedStudent($student,$tutor) , $student);
                $tutor_job = new SendEmailNotification(new AllocatedTutor($student,$tutor),$tutor);

                dispatch($student_job);
                dispatch($tutor_job);
                // end send notification
            }
            
            Log::info("Bulk Imported Successfully");
            DB::commit();
            return response()->json(ResponseModel::Ok(true, "", "Successfully allocated students with tutor."));
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return response()->json(ResponseModel::Failed(false, "", "Some error found please contact developer."));
        }
    }
    public function nonAllocatedStudentList()
    {
        if (auth()->user()->hasAnyRole(['staff', 'tutor'])) {
            $allocated_student_ids = Student::with('allocations')->get();
            $allocated_student_ids = $allocated_student_ids->filter(function ($i, $index) {
                if ($i->allocations->isEmpty()) {
                    return $i;
                }
            })->values();
            return response()->json(ResponseModel::Ok($allocated_student_ids, "", "Successfully fetched the unallocated students."));
        }
    }
}

