<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.index');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('showLoginModal', true)
                ->withInput($request->only('email'));
        }

        // Attempt to authenticate user
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();
            return $this->dashboard(Auth::user());
        }

        return redirect()->back()
            ->withErrors(['email' => 'Invalid email or password.'])
            ->with('showLoginModal', true)
            ->withInput($request->only('email'));
    }


    public function register(Request $request)
    {
        // Define validation rules based on user type
        $rules = [
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|confirmed|min:6',
            'user_type' => 'required|in:student,instructor',
        ];

        // Additional validation for students
        if ($request->user_type === 'student') {
            $rules += [
                'program' => 'required|string',
                'yearsection' => 'required|string',
                'capstone_adviser' => 'required|string',
                'instructor' => 'required|string',
                'members' => 'required|array',
            ];
        }

        // Additional validation for instructors
        if ($request->user_type === 'instructor') {
            $rules += [
                'name' => 'required|string',
                'position' => 'required|string',
            ];
        }

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // If validation fails, return with errors and keep modal open
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator) // Automatically passes validation errors to the form
                ->with('showRegisterModal', $request->user_type) // Keep correct modal open
                ->withInput($request->all()); // Retain input values
        }

        // Prepare data for saving
        $data = [
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
        ];

        // Add extra fields based on user type
        if ($request->user_type === 'student') {
            $data += [
                'program' => $request->program,
                'year_section' => $request->yearsection,
                'capstone_adviser_id' => $request->capstone_adviser,
                'instructor' => $request->instructor,
                'members' => json_encode($request->members ?? []),
            ];
        } elseif ($request->user_type === 'instructor') {
            $data += [
                'name' => $request->name,
                'position' => $request->position,
            ];
        }

        // Create the user
        User::create($data);

        // Redirect back with success message
        return redirect()->back()->with('success', 'Registration successful!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Logged out successfully.');
    }

    public function dashboard($user)
    {
        switch ($user->user_type) {
            case 'admin':
                return redirect('/admin/dashboard')->with('success', 'Login successful!');
            case 'instructor':
                return redirect('/instructor/dashboard')->with('success', 'Login successful!');
            case 'student':
                return redirect('/student/dashboard')->with('success', 'Login successful!');
            default:
                return redirect('/')->withErrors(['error' => 'Unauthorized access.']);
        }
    }
}
