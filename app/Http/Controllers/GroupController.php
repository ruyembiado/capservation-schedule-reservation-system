<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{

    public function create()
    {
        return view('add_group');
    }

    public function viewGroup($id)
    {
        $group = User::where('id', $id)->get()->first();
        $instructors = User::where('user_type', 'instructor')->get();

        return view('view_group', compact('group', 'instructors'));
    }

    public function updateGroupForm($id)
    {
        $group = User::where('id', $id)->get()->first();
        $instructors = User::where('user_type', 'instructor')->get();

        return view('update_group', compact('group', 'instructors'));
    }

    public function updateGroup(Request $request, $id)
    {
        $rules = [
            'email' => 'required|email|unique:users,email,' . $id,
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'members.*' => 'required|string',
            'program' => 'required|string|max:255',
            'yearsection' => 'required|string|max:255',
            'capstone_adviser' => 'required|string|max:255',
            'instructor' => 'required|exists:users,id',
        ];

        $messages = [];
        if ($request->members) {
            foreach ($request->members as $index => $member) {
                $messages["members.$index.required"] = "Member " . ($index + 1) . " is required.";
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $group = User::findOrFail($id);

        $group->email = $request->email;
        $group->username = $request->username;
        $group->program = $request->program;
        $group->year_section = $request->yearsection;
        $group->capstone_adviser = $request->capstone_adviser;
        $group->instructor_id = $request->instructor;

        if ($request->password) {
            $group->password = Hash::make($request->password);
        }

        $group->members = json_encode($request->members);

        $group->save();

        return redirect('/groups')->with('success', 'Group updated successfully!');
    }

    public function deleteGroup($id)
    {
        $group = User::findOrFail($id);
        $group->delete();

        return redirect('/groups')->with('success', 'Group deleted successfully!');
    }
}
