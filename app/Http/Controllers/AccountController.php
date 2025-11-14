<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

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
        $id = Auth::user()->id;

        $user = User::where('id', $id)->first();

        return view('front.accounts.profile', [
            'user' => $user
        ]);
    }

    public function updateProfile(Request $request)
    {

        $id = Auth::user()->id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:5|max:20',
            'email' => 'required|email|unique:users,email,' . $id . ',id'
        ]);

        if ($validator->passes()) {
            $user = User::find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->designation = $request->designation;
            $user->save();

            session()->flash('success', 'Profile Update Successfully.');

            return response()->json([
                'status' => true,
                'errors' => []
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function updateProfilePicture(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image'
        ]);

        $id = Auth::user()->id;

        if ($validator->passes()) {
            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $imageName = $id . '-' . time() . '-' . $ext;
            $image->move(public_path('/profile_picture/'), $imageName);

            // create new image instance (800 x 600)
            $sourcePath = public_path('/profile_picture/'.$imageName);
            $manager = new ImageManager(Driver::class);
            $image = $manager->read($sourcePath);

            // crop the best fitting 5:3 (600x360) ratio and resize to 600x360 pixel
            $image->cover(150, 150);
            $image->toPng()->save(public_path('/profile_picture/thumb/'.$imageName));

            // Delete Old Profile Picture
            File::delete(public_path('/profile_picture/thumb/'.Auth::user()->image));
            File::delete(public_path('/profile_picture/'.Auth::user()->image));

            User::where('id', $id)->update(['image' => $imageName]);

            session()->flash('success', 'Profile Picuter Update Successfully.');

            return response()->json([
                'status' => true,
                'errors' => []
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('accounts.login');
    }
}
