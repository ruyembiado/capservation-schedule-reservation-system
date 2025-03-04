<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function dashboard()
    {
        $data = [];
        if (Auth::user()->user_type === 'admin') {
            $data = [
                'groups' => User::where('user_type', 'student')->get(),
                'instructors' => User::where('user_type', 'instructor')->get(),
                'transactions' => Transaction::all(),
            ];
        } elseif (Auth::user()->user_type === 'instructor') {
            $studentIds = User::where('instructor_id', Auth::user()->id)->pluck('id');
            $data = [
                'groups' => User::whereIn('id', $studentIds)->get(),
                'transactions' => Transaction::whereIn('group_id', $studentIds)->get(),
            ];
        } elseif (Auth::user()->user_type === 'student') {
            $data = [
                'transactions' => Transaction::where('group_id', Auth::user()->id)->get(),
            ];
        }
        return view('dashboard', compact('data'));
    }

    public function index()
    {
        $instructors = User::where('user_type', 'instructor')->get();
        return view('auth.index', compact('instructors'));
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
            return $this->check_user_auth(Auth::user());
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
                'members.*' => 'required|string',
            ];
        }

        // Additional validation for instructors
        if ($request->user_type === 'instructor') {
            $rules += [
                'name' => 'required|string',
                'position' => 'required|string',
            ];
        }

        $messages = [];
        if ($request->user_type === 'student' && $request->members) {
            foreach ($request->members as $index => $member) {
                $messages["members.$index.required"] = "Member " . ($index + 1) . " is required.";
            }
        }

        // Validate the request
        $validator = Validator::make($request->all(), $rules, $messages);

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
                'capstone_adviser' => $request->capstone_adviser,
                'instructor_id' => $request->instructor,
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

    public function check_user_auth($user)
    {
        if ($user) {
            return redirect('/dashboard')->with('success', 'Login successful!');
        } else {
            return redirect('/')->withErrors(['error' => 'Unauthorized access. Please login.']);
        }
    }
}
