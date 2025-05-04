<?php

namespace App\Jobs;

use App\Mail\InactiveStudentEmail;
use App\Models\Allocation;
use App\Models\Arranging;
use App\Notifications\InactiveStudentNotification;
use App\Notifications\InactiveStudentTutorNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Log;

class StudentInactive implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $arranging = Allocation::with([
            'student',
            'tutor',
            'student.blogs' => function ($query) {
                $query->orderBy("created_at", "desc")->limit(1);
            },
            'student.comments' => function ($query) {
                $query->orderBy("created_at", "desc")->limit(1);
            }
        ])->get();

        $cutoffDate = Carbon::now("UTC")->subDays(28);

        $arranging->map(function ($e) use ($cutoffDate) {
            try {
                if ($e->student->blogs->count() > 0) {
                    $latestBlogDate = Carbon::parse($e->student->blogs->first()->created_at);
                    if ($latestBlogDate->greaterThanOrEqualTo($cutoffDate)) {
                        $hasRecentActivity = true;
                    }
                }

                // Check if student has recent comment activity
                if (!$hasRecentActivity && $e->student->comments->count() > 0) {
                    $latestCommentDate = Carbon::parse($e->student->comments->first()->created_at);
                    if ($latestCommentDate->greaterThanOrEqualTo($cutoffDate)) {
                        $hasRecentActivity = true;
                    }
                }

                // If no recent activity, send email
                if (!$hasRecentActivity) {
                    $student_job = new SendEmailNotification(new InactiveStudentNotification($e->student), $e->student);
                    $tutor_job = new SendEmailNotification(new InactiveStudentTutorNotification($e->tutor,$e->student), $e->tutor);
                    dispatch($student_job);
                    dispatch($tutor_job);
                    Log::info("Email sent to inactive student " . $e->student->name . " successfully.");
                }
                Log::info("Sended to the student " . $e->student->name . " successfully.");
            } catch (Exception $exception) {
                Log::error($exception->getMessage());
            }
        });
    }
}