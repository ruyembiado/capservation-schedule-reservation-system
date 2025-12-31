<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Setting;

class PDFController extends Controller
{
    public function downloadPayroll($reservation_id)
	{
		$reservation = Reservation::with('user')->where('id', $reservation_id)->first();
		
		if(!$reservation) {
			return ;
		}
		
		$panelists = User::where('user_type', 'instructor')->get();
		$selectedPanelists = json_decode($reservation->panelist_id, true) ?? [];
		
		$settings = Setting::first();
	    $dean_name = $settings->dean_name ?? "Dean";
	    $program_head_name = null;
	    $dean_title = 'Associate Dean, CSS';
	    $instructor = $reservation->user->instructor->name;
	    $program = strtoupper($reservation->user->program ?? '');
	    $program_title = "Program Head, " . $program;
		
	    switch ($program) {
	        case 'BSIT':
	            $program_head_name = $settings->it_head_name ?? "IT Head";
	            break;
	        case 'BSCS':
	            $program_head_name = $settings->cs_head_name ?? "CS Head";
	            break;
	        case 'BSIS':
	            $program_head_name = $settings->is_head_name ?? "IS Head";
	            break;
	        default:
	            $program_head_name = "Program Head";
	    }
	     
	    $pdf = Pdf::loadView('pdfs.payroll', compact(
	        'reservation',
	        'dean_name',
	        'program_head_name',
	        'program_title',
	        'dean_title',
	        'panelists',
	        'selectedPanelists',
	        'instructor'
	    ))->setPaper('legal', 'landscape');
	
		return $pdf->download('payroll.pdf');
		
		//return view('pdfs.payroll',
	    //compact('reservation','dean_name','program_head_name','program_title','dean_title','panelists','selectedPanelists', 'instructor'));
	}
}
