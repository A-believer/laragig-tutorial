<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    //Show Register Form
    public function create() {
        return view('users.register');
    }


    //Store new user
    public function store(Request $request) {
        $formFields = $request->validate([
            'name' => ['required', 'min:3'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => 'required|confirmed|min:6'
        ]);

        //Hash Password
        $formFields['password'] =  bcrypt($formFields['password']);

        // Create User
        $user = User::create($formFields);

        //Log user in
        auth()->login($user);

        return redirect('/')->with('message', "User logged in!");
    }

    // Show Log in Form
    public function login(){
        return view('users.login');
    }

    //Log users in
    public function authenticate(Request $request) {
        $formFields = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required'
        ]);

        if(auth()->attempt($formFields)) {
            $request->session()->regenerate();

            return redirect('/')->with('message', 'You are now logged in!');
        }

        return back()->withErrors(['email' => 'invalid credentials'])->onlyInput('email');
    }


    //Log out
    public function logout(Request $request) {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message', 'You have been logged out!');
    }
}
