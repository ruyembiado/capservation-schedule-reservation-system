<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $program = $request->input('program');
        $status = $request->input('status');

        if (Auth::user()->user_type === 'instructor') {
            $studentIds = User::where('instructor_id', Auth::user()->id)->pluck('id');
            $query = Transaction::whereIn('group_id', $studentIds);
        } else if (Auth::user()->user_type === 'student') {
            $query = Transaction::where('group_id', Auth::user()->id);
        } else {
            $query = Transaction::query();
        }

        if (!empty($program)) {
            $query->where('program', $program);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }
        $transactions = $query->orderBy('created_at', 'desc')->get();
        return view('transaction', compact('transactions'));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->status = 'paid';
        $transaction->save();
        return redirect()->back()->with('success', 'Transaction marked as paid successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
