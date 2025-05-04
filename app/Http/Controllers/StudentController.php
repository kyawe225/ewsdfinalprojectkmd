<?php
namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{

    // This show all student lists
    public function index()
    {
        $students = Student::whereNotIn('id', function ($query) {
            $query->select('student_id')->from('allocations');
        })->get();

        // dd($students);
        return response()->json([ "data"=>$students]);
    }

    public function getAllStudents()
    {
        $students = Student::orderBy('id')->get();

        return response()->json([ "data"=>$students]);
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('query');

        $students = Student::where(function ($query) use ($searchTerm) {
            $query->where('name', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('email', 'LIKE', '%' . $searchTerm . '%');
        })
            ->whereNotIn('id', function ($query) {
                $query->select('student_id')->from('allocations');
            })
            ->get();

        if ($students->isEmpty()) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return response()->json($students);
    }

    //----------this is to make ascending and descending order based on student name---//
    // public function sortStudents(Request $request)
    // {
    //     $sortDirection = $request->input('sort', 'asc');

    //     if (! in_array($sortDirection, ['asc', 'desc'])) {
    //         return response()->json(['error' => 'Invalid sort direction. Use "asc" or "desc".'], 400);
    //     }

    //     $students = Student::doesntHave('allocation')
    //         ->orderBy('name', $sortDirection)
    //         ->get();

    //     return response()->json($students);
    // }

    // //----------this is to make ascending and descending order based on student id---//
    // public function sortId(Request $request)
    // {
    //     // Get the sort direction from the request (default to 'asc')
    //     $sortDirection = $request->input('sort', 'asc');

    //     // Validate the sort direction to ensure it is either 'asc' or 'desc'
    //     if (! in_array($sortDirection, ['asc', 'desc'])) {
    //         return response()->json(['error' => 'Invalid sort direction. Use "asc" or "desc".'], 400);
    //     }

    //     // Fetch students sorted by name in the specified direction
    //     $students = Student::orderBy('StudentID', $sortDirection)->get();

    //     return response()->json($students);
    // }
}
