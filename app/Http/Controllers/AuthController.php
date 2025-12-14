<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Panelist;
use App\Models\Reservation;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
	public function dashboard() {
		$data = [];
		if (Auth::user()->user_type === 'admin') {
			$data = [
				'groups' => User::where('user_type', 'student')->get(),
				'instructors' => User::where('user_type', 'instructor')->get(),
				'transactions' => Transaction::all(),
				'reservations' => Reservation::all(),
				'panelists' => Panelist::all(),
			];
		} elseif (Auth::user()->user_type === 'instructor') {
			$studentIds = User::where('instructor_id', Auth::user()->id)->pluck('id');
			$data = [
				'groups' => User::whereIn('id', $studentIds)->get(),
				'transactions' => Transaction::whereIn('group_id', $studentIds)->get(),
				'reservations' => Reservation::whereIn('group_id', $studentIds)->get(),
			];
		} elseif (Auth::user()->user_type === 'student') {
			$data = [
				'transactions' => Transaction::where('group_id', Auth::user()->id)->get(),
				'reservations' => Reservation::where('group_id', Auth::user()->id)->get(),
			];
		}
		return view('dashboard', compact('data'));
	}

	public function index() {
		$instructors = User::where('user_type', 'instructor')->get();
		return view('auth.index', compact('instructors'));
	}

	public function login(Request $request) {
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
	        $user = Auth::user();
	
	        if ($user->status == 0) {
	            Auth::logout();
	
	            return redirect()->back()
	                ->withErrors([
	                    'email' => 'Your account is not yet verified. Please wait for admin approval.'
	                ])
	                ->with('showLoginModal', true)
	                ->withInput($request->only('email'));
	        }
	
	        $request->session()->regenerate();
	        return $this->check_user_auth($user);
	    }

		return redirect()->back()
		->withErrors(['email' => 'Invalid email or password.'])
		->with('showLoginModal', true)
		->withInput($request->only('email'));
	}

	public function register(Request $request) {

		// Instructor code validation for students
		if ($request->code != null) {
			$user = User::where('code', $request->code)->first();
			if ($user) {
				$instructor_id = $user->id;
			}
		}

		// Define validation rules based on user type
		$rules = [
			'email' => 'required|email|unique:users,email',
			'username' => 'required|string|unique:users,username',
			'password' => 'required|min:6',
			'password_confirmation' => 'required|same:password',
			'user_type' => 'required|in:student,instructor',
		];

		// Additional validation for students
		if ($request->user_type === 'student') {
			$rules += [
				'program' => 'required|string',
				'yearsection' => 'required|string',
				'capstone_adviser' => 'required|string',
				'code' => 'required|string|exists:users,code',
				'members.*' => 'required|string',
			];
		}

		// Additional validation for instructors
		if ($request->user_type === 'instructor') {
			$rules += [
				'name' => 'required|string',
				'position' => 'required|string',
				'capacity' => 'nullable|integer',
				'credentials.*' => 'nullable|string',
				'vacant_time' => 'nullable|array',
				'status' => 0,
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
				'instructor_id' => $instructor_id,
				'members' => json_encode($request->members ?? []),
			];
		} elseif ($request->user_type === 'instructor') {

			if ($request->has('credentials')) {
				$data['credentials'] = json_encode($request->credentials);
			}

			if ($request->has('vacant_time')) {
				$vacantTimes = [];
				foreach ($request->vacant_time['day'] as $index => $day) {
					$vacantTimes[] = [
						'day' => $day,
						'start_time' => $request->vacant_time['start_time'][$index],
						'end_time' => $request->vacant_time['end_time'][$index],
					];
				}
				$data['vacant_time'] = json_encode($vacantTimes);
			}

			if ($request->capacity) {
				$data['capacity'] = $request->capacity;
			}

			$data += [
				'name' => $request->name,
				'position' => $request->position,
			];
		}

		// Create the user
		$created_user = User::create($data);

		if ($created_user) {
			$activity_logs = new ActivityLog();
			$activity_logs->user_id = $created_user->id;
			$activity_logs->user_type = $created_user->user_type;
			$activity_logs->action = 'Register';
			if ($request->user_type === 'student') {
				$group_name = Str::ucfirst($created_user->username);
				$activity_logs->instructor_id = $request->instructor;
				$activity_logs->description = $group_name . ' registered to the system.';
			} else {
				$activity_logs->description = $created_user->name . ' registered to the system.';
			}
			$activity_logs->save();
		}

		// Redirect back with success message
		return redirect()->back()->with('success', 'Registration successful!');
	}

	public function logout(Request $request) {
		Auth::logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();
		return redirect('/')->with('success', 'Logged out successfully.');
	}

	public function check_user_auth($user) {
		if ($user) {
			return redirect('/dashboard')->with('success', 'Login successful!');
		} else {
			return redirect('/')->withErrors(['error' => 'Unauthorized access. Please login.']);
		}
	}

	public function code() {
		$instructor_id = auth()->user()->id;

		$user = User::where('id', $instructor_id)
		->where('user_type', 'instructor')
		->first();

		return view('code', compact('user'));
	}

	public function addCode(Request $request) {
		$validator = Validator::make($request->all(), [
		'code' => 'required|string',
		]);

		if ($validator->fails()) {
			return redirect()->back()
			->withErrors($validator)
			->withInput();
		}

		$user = User::findOrFail($request->instructor_id);
		$user->code = $request->code;
		$user->save();

		return redirect('/code')->with('success', 'Code added successfully!');
	}

	public function profile() {
		$profile = User::findOrFail(auth()->user()->id);
		$instructors = User::where('user_type', 'instructor')->get();

		return view('profile', compact('profile', 'instructors'));
	}

	public function updateProfile(Request $request, $id) {
		$isAdmin = auth()->user()->user_type === 'admin';

		$rules = [
			'email' => 'required|email',
			'username' => 'required|string|max:255',
			'password' => 'nullable|string|min:8|confirmed',
		];

		if (!$isAdmin) {
			$rules += [
				'members.*' => 'required|string',
				'program' => 'required|string|max:255',
				'yearsection' => 'required|string|max:255',
				'capstone_adviser' => 'required|string|max:255',
				'instructor' => 'required|exists:users,id',
			];
		}

		$messages = [];
		if (!$isAdmin && $request->members) {
			foreach ($request->members as $index => $member) {
				$messages["members.$index.required"] = "Member " . ($index + 1) . " is required.";
			}
		}

		$validator = Validator::make($request->all(), $rules, $messages);

		if ($validator->fails()) {
			return redirect()->back()->withErrors($validator)->withInput();
		}

		$user = User::findOrFail($id);
		$user->email = $request->email;
		$user->username = $request->username;

		if ($request->filled('password')) {
			$user->password = Hash::make($request->password);
		}

		if (!$isAdmin) {
			$user->program = $request->program;
			$user->year_section = $request->yearsection;
			$user->capstone_adviser = $request->capstone_adviser;
			$user->instructor_id = $request->instructor;
			$user->members = json_encode($request->members);
		}

		$user->save();

		return redirect('/profile')->with('success', 'Profile updated successfully!');
	}

	public function sendTestMail() {
		// Get reservation with group_id = 3 and approved status
		$reservation = Reservation::where('group_id', 3)
		->where('status', 'approved')
		->first();

		if (!$reservation) {
			return 'No approved reservation found.';
		}

		// Decode panelist IDs
		$assignedPanelists = json_decode($reservation->panelist_id);

		// Get panelist users
		$panelists = User::whereIn('id', $assignedPanelists)->get();

		if ($panelists->isEmpty()) {
			return 'No panelists found.';
		}

		// Reservation info for email
		$groupName = $reservation->user->username ?? 'Unknown Group';
		$scheduleDate = isset($reservation->latestSchedule)
		? Carbon::parse($reservation->latestSchedule->schedule_date)->format('F j, Y \a\t h:i A')
		: 'No schedule date';

		// Send email to each panelist individually
		foreach ($panelists as $panelist) {
			// Get last name
			$fullNameParts = explode(' ', $panelist->name);
			$lastName = array_pop($fullNameParts); // last word in name

			// Personalized message
			$messageBody = "Hi Mr/Mrs. {$lastName},\n\n"
			. "You are one of the panelists of the group '{$groupName}' and scheduled date on {$scheduleDate}.\n\n"
			. "Please be prepared.";

			$subject = 'Panelist schedule Reminder';

			Mail::raw($messageBody, function ($message) use ($panelist, $subject) {
			$message->to($panelist->email)
			->subject($subject)
			->from('ruyembiadoofficial@gmail.com', 'Capservation');
			});
		}

		return 'Test email sent successfully to all panelists!';
	}
	
	public function active_deactivate(Request $request)
	{
	    $instructor = User::findOrFail($request->id);
	
	    $instructor->status = $request->status;
	    $instructor->save();
	
	    return redirect()->back()->with('success', 'Instructor status updated successfully.');
	}
}