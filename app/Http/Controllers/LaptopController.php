<?php
namespace App\Http\Controllers;

use App\Models\Laptop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LaptopController extends Controller
{
    // Get all usable laptops (status != 'Unusable')
    public function index()
    {   
        $laptops = Laptop::where('status', '!=', 'Unusable')->get();
        // Add full image URLs to each laptop
        $laptops->transform(function ($laptop) {
            if ($laptop->image_path) {
                $laptop->image_url = Storage::url($laptop->image_path);
            }
            return $laptop;
        });
        return response()->json($laptops);
    }

    // Store a new laptop
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'serial_number' => 'required|string|unique:laptops',
            'brand' => 'required|string',
            'model' => 'required|string',
            'status' => 'required|string',
            'condition' => 'required|string',
            'acquired_date' => 'required|date',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048', // validate image
        ]);
    
        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/laptops');
            $imagePath = str_replace('public/', 'storage/', $path); // e.g., storage/laptops/image.jpg
            $validatedData['image_path'] = $imagePath;
        }
    
        $laptop = Laptop::create($validatedData);
    
        return response()->json($laptop, 201);
    }
    

    // Get a specific laptop by ID
    public function show($id)
    {
        $laptop = Laptop::find($id);
        if ($laptop) {
            // Add full image URL if exists
            if ($laptop->image_path) {
                $laptop->image_url = Storage::url($laptop->image_path);
            }
            return response()->json($laptop);
        } else {
            return response()->json(['message' => 'Laptop not found'], 404);
        }
    }

    // Update a laptop
    public function update(Request $request, $id)
{
    $laptop = Laptop::find($id);
    if (!$laptop) {
        return response()->json(['message' => 'Laptop not found'], 404);
    }

    $validatedData = $request->validate([
        'serial_number' => 'sometimes|required|string|unique:laptops,serial_number,'.$id,
        'brand' => 'sometimes|required|string',
        'model' => 'sometimes|required|string',
        'status' => 'sometimes|required|string',
        'condition' => 'sometimes|required|string',
        'acquired_date' => 'sometimes|required|date',
        'image' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048', // same validation as store
    ]);

    // Handle image upload if present
    if ($request->hasFile('image')) {
        // Delete old image if exists
        if ($laptop->image_path) {
            $oldPath = str_replace('storage/', 'public/', $laptop->image_path);
            Storage::delete($oldPath);
        }

        // Store new image (matching store() method behavior)
        $path = $request->file('image')->store('public/laptops');
        $imagePath = str_replace('public/', 'storage/', $path);
        $validatedData['image_path'] = $imagePath;
    }

    $laptop->update($validatedData);

    return response()->json($laptop);
}

    // Delete a laptop (mark as Unusable)
    public function destroy($id)
    {
        $laptop = Laptop::find($id);
        
        if ($laptop) {
            // Delete associated image if exists
            if ($laptop->image_path) {
                Storage::disk('public')->delete($laptop->image_path);
            }
            
            $laptop->update(['status' => 'Unusable']);
            return response()->json(['message' => 'Laptop marked as Unusable']);
        } else {
            return response()->json(['message' => 'Laptop not found'], 404);
        }
    }

    // Upload image for a laptop
    public function uploadImage(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $laptop = Laptop::find($id);
        if (!$laptop) {
            return response()->json(['message' => 'Laptop not found'], 404);
        }

        // Delete old image if exists
        if ($laptop->image_path) {
            Storage::disk('public')->delete($laptop->image_path);
        }

        // Generate unique filename
        $extension = $request->file('image')->getClientOriginalExtension();
        $filename = Str::slug($laptop->brand . '-' . $laptop->model . '-' . $laptop->serial_number) . '-' . time() . '.' . $extension;

        // Store image in public storage
        $request->file('image')->storeAs(
            'laptops', 
            $filename,
            'public'
        );

        $laptop->update(['image_path' => $filename]);

        return response()->json([
            'message' => 'Image uploaded successfully',
            'image_url' => Storage::url($filename)
        ]);
    }

    // Get laptop image
    public function getImage($id)
    {
        $laptop = Laptop::find($id);
        if (!$laptop || !$laptop->image_path) {
            return response()->json(['message' => 'Image not found'], 404);
        }
        
        $path = storage_path('app/public/' . $laptop->image_path);
        
        if (!file_exists($path)) {
            return response()->json(['message' => 'Image file not found'], 404);
        }

        return response()->file($path);
    }

    // Remove laptop image
    public function removeImage($id)
    {
        $laptop = Laptop::find($id);
        if (!$laptop) {
            return response()->json(['message' => 'Laptop not found'], 404);
        }

        if (!$laptop->image_path) {
            return response()->json(['message' => 'No image to remove'], 400);
        }

        Storage::disk('public')->delete($laptop->image_path);
        $laptop->update(['image_path' => null]);

        return response()->json(['message' => 'Image removed successfully']);
    }
}
