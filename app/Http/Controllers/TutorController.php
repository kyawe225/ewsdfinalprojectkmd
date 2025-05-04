<?php

namespace App\Http\Controllers;

use App\Models\Tutor;
use Illuminate\Http\Request;

class TutorController extends Controller
{
    /**
     * Get a list of all tutors.
     */
    public function tutorList()
    {
        $tutors = Tutor::select('id', 'name', 'email', 'specialization')->get();

        return response()->json(['tutors' => $tutors], 200);
    }
}

