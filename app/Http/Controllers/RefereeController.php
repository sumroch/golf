<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class RefereeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('admin.master.referee', [
            'referees' => User::with('roles')->orderBy('id', 'asc')->withoutRole('admin')->paginate(10),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'acronym' => $request->input('acronym'),
            'username' => $request->input('acronym'),
            'phone_number' => $request->input('phone_number'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);
        
        $user->assignRole($request->input('position', 'referee'));

        return redirect()->route('referee.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'name' => $request->input('name'),
            'acronym' => $request->input('acronym'),
            'phone_number' => $request->input('phone_number'),
            'username' => $request->input('acronym'),
            'email' => $request->input('email'),
            'password' =>  bcrypt($request->input('password')),
        ]);

        $user->syncRoles($request->input('position', 'referee'));

        return redirect()->route('referee.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($user)
    {
        $user = User::findOrFail($user);
        $user->delete();

        return redirect()->route('referee.index');
    }
}
