<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Laptop;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class RentalController extends Controller
{
    // Get all rentals
    public function index()
    {
        $rentals = Rental::with(['laptop', 'student'])->get();
        return response()->json($rentals);      
    }

     public function store(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'laptop_id' => 'required|exists:laptops,id',
            'student_id' => 'required|exists:students,id',
            'rental_date' => 'required|date',
                'return_date' => 'required|date|after_or_equal:rental_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if laptop is available
        $laptop = Laptop::find($request->laptop_id);
        if ($laptop->status !== 'Available') {
            return response()->json([
                'message' => 'Laptop is not available for rent.'
            ], 400);
        }

        // Create the rental record
        $rental = Rental::create([
            'laptop_id' => $request->laptop_id,
            'student_id' => $request->student_id,
            'rental_date' => $request->rental_date,
            'return_date' => $request->return_date,
            'status' => 'Approval'
        ]);

        // Update the laptop status to Rented
        $laptop->status = 'Rented';
        $laptop->save();

        return response()->json([
            'message' => 'Laptop rented successfully.',
            'rental' => $rental
        ], 201);
    }


// app/Http/Controllers/RentalController.php
public function return($id)
{
    $rental = Rental::findOrFail($id);
    
    // Validate status transition
    if (!in_array($rental->status, ['Active', 'Overdue'])) {
        return response()->json([
            'message' => 'Only Active or Overdue rentals can be returned'
        ], 400);
    }

    $now = now(); // Get current timestamp once
    
    $rental->update([
        'status' => 'Completed',
        'date_returned' => $now, // Actual return date
    ]);

    // Update laptop status to available
    if ($rental->laptop) {
        $rental->laptop->update(['status' => 'Available']);
    }

    return response()->json([
        'message' => 'Rental returned successfully',
        'data' => $rental->fresh() // Return refreshed data
    ]);
}

// app/Http/Controllers/RentalController.php
public function cancel($id)
{
    $rental = Rental::findOrFail($id);
    
    // Validate status transition
    if ($rental->status !== 'Approval') {
        return response()->json([
            'message' => 'Only rentals in Approval status can be canceled'
        ], 400);
    }

    $rental->update([
        'status' => 'Canceled'
    ]);

    return response()->json([
        'message' => 'Rental canceled successfully',
        'data' => $rental
    ]);
}


public function approve($id)
{
    $rental = Rental::findOrFail($id);
    
    // Validate status transition
    if ($rental->status !== 'Approval') {
        return response()->json([
            'message' => 'Only rentals in Approval status can be approved'
        ], 400);
    }

    $rental->update([
        'status' => 'Active'
      
    ]);

    $rental->laptop()->update(['status' => 'Rented']);

    return response()->json([
        'message' => 'Rental approved successfully',
        'data' => $rental
    ]);
}

    // Get a specific rental
    public function show($id)
    {
        $rental = Rental::with(['laptop', 'student'])->find($id);
        if (!$rental) {
            return response()->json(['message' => 'Rental not found'], 404);
        }
        return response()->json($rental);
    }

    // Update a rental (mainly for returns)
    public function update(Request $request, $id)
    {
        $rental = Rental::find($id);
        if (!$rental) {
            return response()->json(['message' => 'Rental not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'return_date' => 'sometimes|date',
            'condition_after' => 'sometimes|string',
            'status' => 'sometimes|in:active,completed,overdue',
            'rental_notes' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Handle rental return
        if ($request->has('return_date') && $request->has('condition_after')) {
            $rental->update([
                'return_date' => $request->return_date,
                'condition_after' => $request->condition_after,
                'status' => 'completed'
            ]);

            // Update laptop status
            $laptop = Laptop::find($rental->laptop_id);
            $laptop->update(['status' => 'available']);
        } else {
            $rental->update($request->all());
        }

        return response()->json($rental);
    }

    // Get rentals by student ID
    public function getByStudent($studentId)
    {
        $rentals = Rental::with(['laptop'])
            ->where('student_id', $studentId)
            ->orderBy('rental_date', 'desc')
            ->get();

        return response()->json($rentals);
    }

    // Get active rentals
    public function getActiveRentals()
    {
        $rentals = Rental::with(['laptop', 'student'])
            ->where('status', 'active')
            ->get();

        return response()->json($rentals);
    }

    // Check overdue rentals
    public function checkOverdueRentals()
    {
        $now = Carbon::now();
        $rentals = Rental::with(['laptop', 'student'])
            ->where('status', 'active')
            ->where('due_date', '<', $now)
            ->get();

        // Update status to overdue
        foreach ($rentals as $rental) {
            $rental->update(['status' => 'overdue']);
        }

        return response()->json($rentals);
    }
}   