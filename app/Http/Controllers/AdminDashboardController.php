<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\LoginLog;
use App\Models\Student;
use App\Models\Tutor;
use App\ResponseModel\ResponseModel;
use Carbon\Carbon;
use DB;

class AdminDashboardController
{
    public function index()
    {
        $total_students = Student::count();
        $total_tutors = Tutor::count();
        $total_unassigned_students = Student::whereNotIn('id', function ($query) {
            $query->select('student_id')->from('allocations');
        })->count();
        $most_used_browsers = LoginLog::groupBy('browser')->where('browser' ,"<>","0")->get(['browser', DB::raw("COUNT(ip_address)")]);
        $now = Carbon::now();
        $start_of_week = $now->subMonths(1);
        $most_active_users = Student::withCount(['blogs'=>function($query) use ($start_of_week){
            $query->where('created_at',">=",$start_of_week);
        },'comments'=>function($query) use ($start_of_week){
            $query->where('created_at',">=",$start_of_week);
        }])->where(DB::raw("DATE(created_at)"),">=",$start_of_week)->groupBy("id")->get();
        $most_active_users = $most_active_users->map(function($p){
            $counts= $p->blogs_count+$p->comments_count;
            $p['count'] = $counts;
            return $p;
        })->sortByDesc('count')->take(10)->values();
        $avaliable_teacher = Tutor::has('allocations','<=',30)->withCount(['allocations'])->get(); 
        $avaliable_teacher = $avaliable_teacher->sortByDesc('allocations_count')->values();

        $staff = auth()->user();

        // Prepare the user information.
        $userInfo = [
            'name'          => $staff->name,
            'email'         => $staff->email,
            'last_login_at' => $staff->last_login_at,
        ];

        return response()->json(ResponseModel::Ok(["userInfo" => $userInfo,"total_students" => $total_students, "total_tutors" => $total_tutors, "total_unassigned_students" => $total_unassigned_students, "browsers" => $most_used_browsers, "active_users" => $most_active_users , 'avaliable_teacher'=> $avaliable_teacher], "", "dashboard fetched successfully"));
    }
}