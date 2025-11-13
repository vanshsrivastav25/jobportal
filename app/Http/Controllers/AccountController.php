<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    // This method will show our registration page
    public function registration()
    {
        return view('front.accounts.registration');
    }

    // this method will save a user data in database
    public function processRegistration(request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5|same:confirm_password',
            'confirm_password' => 'required',
        ]);

        if ($validator->passes()) {

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            session()->flash('success', 'You have registed sucessfully.');

            return response()->json([
                'status' => true,
                'errors' => [],
                'redirect_url' => route('accounts.login')
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    // This method will show our login page
    public function login()
    {
        return view('front.accounts.login');
    }


    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->passes()) {

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return redirect()->route('accounts.profile');
            } else {
                return redirect()->route('accounts.login')->with('error', 'Either Email/Password is incorrect');
            }
        } else {
            return redirect()->route('accounts.login')
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }
    }

    public function profile()
    {
        return view('front.accounts.profile');
    }

    public function logout () {
        Auth::logout();
        return redirect()->route('accounts.login');
    }
}
