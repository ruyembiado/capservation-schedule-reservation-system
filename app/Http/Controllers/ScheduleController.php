<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use App\Models\Setting;
use App\Models\Reservation;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\ReservationHistory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ScheduleController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		$reservations = Reservation::where("status", "approved")
			->with("user")
			->get();

		return view("calendar", compact("reservations"));
	}

	public function getSchedules()
	{
		$query = Schedule::with('user')
			->orderBy('schedule_date', 'ASC');

		if (auth()->user()->user_type === 'panelist') {
			$panelistId = auth()->user()->id;

			$reservationIds = Reservation::where('status', 'approved')
				->where(function ($q) use ($panelistId) {
					$q->whereRaw("panelist_id = ?", ['[' . $panelistId . ']'])
						->orWhereRaw("panelist_id LIKE ?", ['[' . $panelistId . ',%'])
						->orWhereRaw("panelist_id LIKE ?", ['%,' . $panelistId . ',%'])
						->orWhereRaw("panelist_id LIKE ?", ['%,' . $panelistId . ']']);
				})
				->pluck('id');

			// Include both:
			// - schedules linked to panelist's reservations
			// - OR schedules marked as unavailable
			$query->where(function ($q) use ($reservationIds) {
				$q->whereIn('reservation_id', $reservationIds)
					->orWhere('schedule_category', 'unavailable');
			});
		}

		$schedules = $query->get()->map(function ($schedule) {

			$isUnavailable = $schedule->schedule_category === 'unavailable';

			if ($isUnavailable) {
				$start = $schedule->schedule_date;
				$end   = date('Y-m-d', strtotime($schedule->schedule_date . ' +1 day'));

				return [
					'id' => $schedule->id,
					'title' => '',
					'start' => $start,
					'end' => $end,
					'allDay' => true,
					'display' => 'background',
					'backgroundColor' => '#DC3545',
					'borderColor' => '#DC3545',
					'textColor' => '#ffffff',
					'isUnavailable' => true,
				];
			}

			return [
				'id' => $schedule->id,
				'title' => ucwords(optional($schedule->user)->username ?? 'Unknown User'),
				'start' => $schedule->schedule_date . 'T' . $schedule->schedule_time,
				'end'   => $schedule->schedule_date . 'T' .
					date('H:i:s', strtotime($schedule->schedule_time . ' +1 hour')),
				'allDay' => false,
				'isUnavailable' => false,
			];
		});

		return response()->json($schedules);
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			"group" => "nullable|exists:users,id",
			"schedule_date" => "required|date",
			"schedule_time" => "nullable|date_format:H:i",
			"schedule_category" => "nullable|in:available,occupied,unavailable",
			"schedule_remarks" => "nullable|string|max:255",
		]);

		$validator->sometimes(["group", "schedule_time"], "required", function (
			$input
		) {
			return empty($input->schedule_category);
		});

		if ($validator->fails()) {
			return redirect()
				->back()
				->withErrors($validator)
				->withInput($request->all());
		}

		$reservation = Reservation::where("group_id", $request->group)
			->where("status", "approved")
			->first();

		if ($reservation) {
			$reservation->status = "reserved";
			$reservation->save();

			Schedule::create([
				"group_id" => $request->group ?: null,
				"reservation_id" => $reservation->id,
				"schedule_date" => $request->schedule_date,
				"schedule_time" => $request->schedule_time ?: null,
				"schedule_category" => $request->schedule_category ?: "",
				"schedule_remarks" => $request->schedule_remarks ?: "",
			]);

			Notification::create([
				"user_id" => $request->group,
				"_link_id" => $reservation->id,
				"notification_type" => "system_alert",
				"notification_title" => "Schedule Created",
				"notification_message" =>
				ucfirst($reservation->user->username) .
					'\'s reservation has been scheduled for defense.',
			]);

			$assignedPanelists = json_decode($reservation->panelist_id);

			// Send mail to panelists
			$mail = $this->sendPanelistEmail($reservation, $assignedPanelists);
		} else {
			Schedule::create([
				"group_id" => null,
				"reservation_id" => null,
				"schedule_date" => $request->schedule_date,
				"schedule_time" => null,
				"schedule_category" => $request->schedule_category ?: "",
				"schedule_remarks" => $request->schedule_remarks ?: "",
			]);

			$schedules = Schedule::where(
				"schedule_date",
				$request->schedule_date
			)->get();

			foreach ($schedules as $schedule) {
				if ($schedule->reservation_id) {
					ReservationHistory::create([
						"reservation_id" => $schedule->reservation_id,
					]);
				}
			}

			Reservation::whereIn("id", $schedules->pluck("reservation_id"))->update([
				"status" => "approved",
			]);

			$reservations = Reservation::whereIn(
				"id",
				$schedules->pluck("reservation_id")
			)->get();

			foreach ($reservations as $reservation) {
				Notification::create([
					"user_id" => $reservation->group_id,
					"_link_id" => $reservation->id,
					"notification_type" => "system_alert",
					"notification_title" => "Defense Recheduled",
					"notification_message" =>
					ucfirst($reservation->user->username) .
						'\'s defense scheduled on ' .
						$request->schedule_date .
						" has been cancelled and will be rescheduled.",
				]);

				$panelistIds = json_decode($reservation->panelist_id, true);
				if (is_array($panelistIds)) {
					foreach ($panelistIds as $panelistId) {
						Notification::create([
							'user_id' => $panelistId,
							'_link_id' => $reservation->id,
							"notification_type" => "system_alert",
							"notification_title" => "Defense Recheduled",
							"notification_message" =>
							ucfirst($reservation->user->username) .
								'\'s defense scheduled on ' .
								$request->schedule_date .
								" has been cancelled and will be rescheduled.",
						]);
					}
				}
			}

			return redirect()
				->back()
				->with(
					"success",
					"All defenses scheduled on " .
						$request->schedule_date .
						" have been cancelled and will be rescheduled.",
				);
		}

		return redirect()
			->back()
			->with("success", "Schedule created successfully.");
	}

	/**
	 * Display the specified resource.
	 */
	public function show(Schedule $schedule)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Schedule $schedule)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request)
	{
		$reservation = Reservation::findOrFail($request->id);

		//ReservationHistory::create([
		//"reservation_id" => $reservation->id,
		//]);
		$schedule = Schedule::where('reservation_id', $request->id)->first();
		$schedule->delete();

		$reservation->status = "pending";
		$reservation->save();

		Notification::create([
			"user_id" => $reservation->group_id,
			"_link_id" => $reservation->id,
			"notification_type" => "system_alert",
			"notification_title" => "Reservation Re-scheduled",
			"notification_message" =>
			ucfirst($reservation->user->username) .
				'\'s reservation has been re-scheduled and set to approved.',
		]);

		return redirect()
			->back()
			->with("success", "Reservation Re-scheduled.");
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Schedule $schedule)
	{
		//
	}

	public function sendPanelistEmail($reservation, $assignedPanelists)
	{
		if (empty($assignedPanelists)) {
			return false;
		}

		// Get panelist users
		$panelists = User::whereIn("id", $assignedPanelists)->get();

		if ($panelists->isEmpty()) {
			return false;
		}
		// Reload reservation with latest schedule
		$reservation = Reservation::with("latestSchedule")->find($reservation->id);
		$groupName = ucwords($reservation->user->username ?? "Unknown Group");

		if ($reservation->latestSchedule) {
			$scheduleDate = Carbon::parse(
				$reservation->latestSchedule->schedule_date
			)->format("F j, Y"); // e.g., September 24, 2025
			$scheduleTime = Carbon::parse(
				$reservation->latestSchedule->schedule_time
			)->format("h:i A"); // e.g., 02:30 PM
		} else {
			$scheduleDate = "No schedule date";
			$scheduleTime = "";
		}

		foreach ($panelists as $panelist) {
			$fullNameParts = explode(" ", $panelist->name);
			$lastName = array_pop($fullNameParts);

			$messageBody =
				"Hi Mr/Mrs. {$lastName},\n\n" .
				"You are one of the panelists of the group {$groupName} " .
				"and scheduled date on {$scheduleDate} at {$scheduleTime}.\n\n" .
				"Please be prepared.";

			$subject = "Panelist Schedule Reminder";

			Mail::raw($messageBody, function ($message) use ($panelist, $subject) {
				$message
					->to($panelist->email)
					->subject($subject)
					->from(config('mail.from.address'), config('mail.from.name'));
			});
		}

		return true;
	}

	public function sendDeanHeadEmail($reservation)
	{
		$reservation = Reservation::with('latestSchedule', 'user')->find($reservation->id);
		if (!$reservation) {
			return false; // reservation not found
		}

		$groupName = ucwords($reservation->user->username ?? "Unknown Group");

		if ($reservation->latestSchedule) {
			$scheduleDate = Carbon::parse($reservation->latestSchedule->schedule_date)->format('F j, Y');
			$scheduleTime = Carbon::parse($reservation->latestSchedule->schedule_time)->format('h:i A');
		} else {
			$scheduleDate = "No schedule date";
			$scheduleTime = "";
		}

		$settings = Setting::first();
		if (!$settings) {
			return false;
		}

		$program = strtoupper($reservation->user->program ?? '');
		switch ($program) {
			case 'BSIT':
				$programHeadName = $settings->it_head_name ?? null;
				$programHeadEmail = $settings->it_head_email ?? null;
				break;
			case 'BSCS':
				$programHeadName = $settings->cs_head_name ?? null;
				$programHeadEmail = $settings->cs_head_email ?? null;
				break;
			case 'BSIS':
				$programHeadName = $settings->is_head_name ?? null;
				$programHeadEmail = $settings->is_head_email ?? null;
				break;
			default:
				$programHeadName = null;
				$programHeadEmail = null;
		}

		$sendEmail = function ($recipientName, $recipientEmail) use ($groupName, $scheduleDate, $scheduleTime) {
			$messageBody = "Hi Mr/Mrs. {$recipientName},\n\n" .
				"You are one of the panelists of the group {$groupName} " .
				"and scheduled date on {$scheduleDate} at {$scheduleTime}.\n\n" .
				"Please be prepared.";

			$subject = "Panelist Schedule Reminder";

			Mail::raw($messageBody, function ($message) use ($recipientEmail, $subject) {
				$message->to($recipientEmail)
					->subject($subject)
					->from(config('mail.from.address'), config('mail.from.name'));
			});
		};

		// Send to Dean
		if ($settings->dean_email) {
			$sendEmail($settings->dean_name ?? "Dean", $settings->dean_email);
		}

		// Send to Program Head
		if ($programHeadEmail) {
			$sendEmail($programHeadName ?? "Program Head", $programHeadEmail);
		}

		return true;
	}
}
