<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create()
    {
        return view('login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $result = Auth::attempt($request->only('email', 'password'), $request->boolean('remember'));
//        $user = User::where('email', '=', $request->email)->first();

//        if ($user && Hash::check($request->password, $user->password)) {
//            // Authenticated
//            Auth::login($user, $request->remember); // here create session and inside it data refer the current user authenticated.
//            // here possible write  $request->boolean('remember') another way.
//            return redirect()->route('classrooms.index');
//        }

        if ($result) {
            return redirect()->route('classrooms.index');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }
}
