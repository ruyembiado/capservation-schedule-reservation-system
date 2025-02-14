<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
}
