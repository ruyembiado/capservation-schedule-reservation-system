<?php

namespace App\Http\Controllers;

use App\Models\Panelist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PanelistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $panelists = Panelist::all();
        return view('panelist', compact('panelists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('add_panelist');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:panelists',
            'email' => 'required|email|unique:panelists',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->all());
        }

        Panelist::create($request->all());

        return redirect()->back()->with('success', 'Panelist added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Panelist $panelist)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Panelist $panelist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Panelist $panelist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Panelist $panelist)
    {
        //
    }
}
