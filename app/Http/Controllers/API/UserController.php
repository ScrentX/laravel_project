<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        // $validated = $request->validate([
        //     'username' => 'required|string|unique:user',
        //     'password' => 'required|string|min:6',
        // ]);
        //admin side
        $user = User::create([
            'username' => $request['username'],
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'User created successfully'], 201);
    }
    public function login(Request $request)
    {
        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return $user->createToken('react-token')->plainTextToken;
    }
    public function logout (Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
    
            public function studentLogin(Request $request)
        {
            $validated = $request->validate([
                'student_id' => 'required|string',
                'password' => 'required|string'
            ]);

            $user = Student::where('student_id', $validated['student_id'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'message' => 'The provided credentials are incorrect.'
                ], 401);
            }

            $token = $user->createToken('react-token', ['student'])->plainTextToken;

            return response()->json([
                'user' => $user->only(['id', 'name', 'email']),
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]);
        }

    public function rentLaptop(Request $request)
    {
        $student = auth()->user();

        $laptop = Laptop::find($request->laptop_id);

        if (!$laptop) {
            return response()->json(['message' => 'Laptop not found'], 404);
        }

        $rental = new Rental();
        $rental->student_id = $student->id;
        $rental->laptop_id = $laptop->id;
        $rental->rental_date = date('Y-m-d');
        $rental->return_date = date('Y-m-d', strtotime('+7 days'));
        $rental->status = 'Active';
        $rental->save();

        return response()->json(['message' => 'Laptop rented successfully'], 200);
    }

    public function getRentals(Request $request)
    {
        $student = auth()->user();

        $rentals = Rental::where('student_id', $student->id)->get();

        return response()->json($rentals, 200);
    }
    

    public function showUser()
    {
     $user = User::all();
      return response()->json([
        'list_of_user' => $user,
        'status' => 200,
        'message' => "Success"
      ]);
    }

}
