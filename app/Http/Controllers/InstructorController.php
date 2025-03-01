<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class InstructorController extends Controller
{
    public function instructorDashboard()
    {
        return view('instructor.dashboard');
    }

    public function get_instructors(Request $request)
    {
        try {
            $search = $request->input('search');

            $instructors = User::where('user_type', 'instructor')
                ->when($search, function ($query) use ($search) {
                    return $query->where('name', 'LIKE', "%{$search}%");
                })
                ->get(['id', 'name']);

            return response()->json($instructors);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateInstructorForm($id)
    {
        $instructor = User::where('id', $id)->get()->first();

        return view('update_instructor', compact('instructor'));
    }

    public function updateInstructor(Request $request, $id)
    {
        // Define validation rules
        $rules = [
            'email' => 'required|email|unique:users,email,' . $id,
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $instructor = User::findOrFail($id);

        $instructor->email = $request->email;
        $instructor->username = $request->username;
        $instructor->name = $request->name;
        $instructor->position = $request->position;

        if ($request->password) {
            $instructor->password = Hash::make($request->password);
        }

        $instructor->save();

        return redirect('/instructors')->with('success', 'Instructor updated successfully!');
    }

    public function deleteInstructor($id)
    {
        $instructor = User::findOrFail($id);
        $instructor->delete();

        return redirect('/instructors')->with('success', 'Instructor deleted successfully!');
    }
}
