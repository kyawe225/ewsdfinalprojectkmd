<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\Student;
use App\ResponseModel\ResponseModel;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    public function register(RegisterRequest $registerRequest){
        try{
            $validatedData = $registerRequest->validated();

            $model = [
                "name" => $validatedData['name'], // ✅ Fix: Use 'name' instead of 'username'
                "email" => $validatedData['email'],
                "password" => bcrypt($validatedData['password']),
                "phone_number" => $validatedData['phone_number']
            ];

            $data = Student::create($model);

            return response()->json(ResponseModel::Ok("Registered Successfully", $data->id, "Registered Successfully"));
        } catch(\Exception $e) {
            Log::error("RegisterController.register => " . $e->getMessage()); // ✅ Fix string concatenation
            return response()->json(ResponseModel::Failed("Internal Error", "", $e->getMessage()));
        }
    }
}

