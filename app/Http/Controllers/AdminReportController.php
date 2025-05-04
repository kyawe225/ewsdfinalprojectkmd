<?php
namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Comments;
use App\Models\Student;
use App\Models\Tutor;
use Carbon\Carbon;

class AdminReportController extends Controller
{
    public function AdminReport()
    {
        // Retrieve all students that have at least one allocation.
        // We eager-load the tutor through the allocation, blogs, and comments.
        $blogs    = $this->getAverageMessageToStudents();
        $students = Student::whereHas('allocations')
            ->with(['allocations.tutor', 'blogs', 'comments'])
            ->get();

        //    dd($students);

        /**
         * Group 1: Students with allocation made but no login.
         * - Criteria: last_login_at is null.
         * - Inactive days are computed from the allocation date.
         */
        $groupNoLogin = $students->filter(function ($student) {
            // Only consider students that have never logged in.
            if ($student->last_login_at !== null) {
                return false;
            }
            // Use the allocation date from the first allocation record.
            $allocation = $student->allocations->first();
            if (! $allocation || ! $allocation->allocation_date) {
                return false;
            }
            $allocationDate = Carbon::parse($allocation->allocation_date);
            $inactiveDays   = $allocationDate->diffInDays(Carbon::now());
            return $inactiveDays > 7;
        })->map(function ($student) {
            $allocation     = $student->allocations->first();
            $allocationDate = Carbon::parse($allocation->allocation_date);
            $inactiveDays   = $allocationDate->diffInDays(Carbon::now());
            $tutorName      = isset($allocation->tutor->name) ? $allocation->tutor->name : null;

            return [
                'student_code'  => $student->StudentID,
                'email'         => $student->email,
                'last_login'    => "No login",
                'inactive_days' => $inactiveDays,
                'tutor_name'    => $tutorName,
            ];
        })->values(); // Re-index the collection

        //  dd($groupNoLogin);

        /**
         * Group 2: Students with allocation made and login recorded.
         * Inactive days are computed as follows:
         * - If the student has created any blog or comment records, use the later of the blog or comment's created date.
         * - Otherwise, use the allocation date.
         */
        $groupLoginCalculated = [];

        foreach ($students as $student) {
            // Skip only if the student has never logged in AND has never posted.
            if (is_null($student->last_login_at)
                && $student->blogs->isEmpty()
                && $student->comments->isEmpty()
            ) {
                continue;
            }

            // Gather all possible "activity" dates:
            $dates = [];

            // 1) If they ever logged in, include that.
            if (! is_null($student->last_login_at)) {
                $dates[] = Carbon::parse($student->last_login_at);
            }

            // 2) Their allocation date, if any.
            if ($allocation = $student->allocations->first()) {
                if ($allocation->allocation_date) {
                    $dates[] = Carbon::parse($allocation->allocation_date);
                }
            }

            // 3) Most recent blog post (if any)
            if ($student->blogs->isNotEmpty()) {
                $dates[] = Carbon::parse($student->blogs->max('created_at'));
            }

            // 4) Most recent comment (if any)
            if ($student->comments->isNotEmpty()) {
                $dates[] = Carbon::parse($student->comments->max('created_at'));
            }

            // If for some reason we collected no dates, skip.
            if (empty($dates)) {
                continue;
            }

            // Find the latest of all candidate dates:
            /** @var \Carbon\Carbon $lastActivity */
            $lastActivity = array_reduce($dates, function ($carry, Carbon $d) {
                return $carry === null || $d->greaterThan($carry) ? $d : $carry;
            }, null);

            // Calculate inactivity
            $inactiveDays = $lastActivity->diffInDays(Carbon::now());

            // Only report those inactive more than 7 days
            if ($inactiveDays > 7) {
                $groupLoginCalculated[] = [
                    'student_code'  => $student->StudentID,
                    'name'          => $student->name,
                    'email'         => $student->email,
                    'last_active'   => optional($student->last_login_at)
                    ? Carbon::parse($student->last_login_at)->format('Y-m-d')
                    : null,
                    'inactive_days' => $inactiveDays,
                    'tutor_name'    => optional($allocation->tutor)->name,
                ];
            }
        }

        // For debugging: Uncomment the following line to inspect the groupLoginCalculated data.
        //dd($groupLoginCalculated);

        $studentsWithoutTutor = Student::whereDoesntHave('allocations', function ($query) {
            $query->whereNotNull('tutor_id');
        })->get();

        // Return both groups in a JSON response.
        return response()->json([
            'Average_Interaction'           => $blogs,
            // 'group_no_login'                => $groupNoLogin,
            'group_login_calculated'        => $groupLoginCalculated,
            'student_without_personalTutor' => $studentsWithoutTutor,
        ]);
    }

    private function getAverageMessageToStudents()
    {
        $blogs    = Blog::where("author_role", 'tutor')->with("comments")->get();
        $blogs    = $blogs->sortBy(["author", "author_role"]);
        $blogs    = $blogs->countBy('author');
        $comments = Comments::with("tutor")->whereNotNull('tutor_id')->get();
        $comments = $comments->countBy('tutor.name');
        $blogs    = $blogs->merge($comments)->map(function ($value, $key) use ($comments) {
            if ($comments->has($key)) {
                return $value + $comments->get($key);
            }
            return $value;
        });
        $blogs = $blogs->map(function ($value, $key) {
            $tutor = Tutor::where("name", $key)->first();
            $mth   = ceil(Carbon::parse($tutor->created_at, "UTC")->diffInMonths(Carbon::now('UTC')));
            return $value / $mth;
        });
        return $blogs;
    }
}
