<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CreateStudentsTableController extends Controller
{
    // Get all students
    public function index()
    {
        return Student::all();
    }

    // Get single student
    public function show($id)
    {
        return Student::findOrFail($id);
    }

    // Create new student
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|unique:students',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:students',
            'course' => 'required|in:BSBA,BSIT,BSED',
            'year_level' => 'required',
            'contact_number' => 'required',
            'password' => 'required|min:8'
        ]);

        $validated['password'] = Hash::make($validated['password']);

        return Student::create($validated);
    }

    // Update student
    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'student_id' => 'sometimes|required|unique:students,student_id,'.$student->id,
            'first_name' => 'sometimes|required',
            'last_name' => 'sometimes|required',
            'email' => 'sometimes|required|email|unique:students,email,'.$student->id,
            'course' => 'sometimes|required|in:BSBA,BSIT,BSED',
            'year_level' => 'sometimes|required',
            'contact_number' => 'sometimes|required',
            'password' => 'sometimes|min:6'
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $student->update($validated);

        return $student;
    }

    // Delete student
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return response()->json(['message' => 'Student deleted successfully']);
    }

    // Search students
    public function search(Request $request)
    {
        $term = $request->query('term');

        return Student::where('first_name', 'like', "%$term%")
            ->orWhere('last_name', 'like', "%$term%")
            ->orWhere('student_id', 'like', "%$term%")
            ->get();
    }

    public function createStudent(Request $request)
{
    return $this->store($request);
}


    
}