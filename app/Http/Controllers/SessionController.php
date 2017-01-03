<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SessionController extends Controller
{
    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        if (Auth::attempt(['username' => $username, 'password' => $password])) {
            return redirect('events/' . Auth::user()->id);
        }

        return view('login')->with('error', 'Login failed');
    }

    public function logout()
    {
        Auth::logout();

        return view('login');
    }
}
