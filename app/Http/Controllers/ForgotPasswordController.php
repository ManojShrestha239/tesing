<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{

    public function showForgetPasswordForm()
    {
        return view('resetpassword.forgetPassword');
    }

    public function submitForgetPasswordForm(Request $request)
    {
        // $valid = Validator($request->all(),[
        //     'email' => 'required|email|exists:users',
        // ]);
        // if($valid->fails()){
        //     return back()->withInput()->with($valid->errors());
        // }
        $request->validate([
            'email' => 'required|email|exists:users'
        ]);

        //create random token
        $token = Str::random(64);

        //storing in database send token of user emails
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        //Send mail to user email
        Mail::send('resetpassword.emails.forgetPassword', ['token' => $token], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Password');
        });
        // return response($token);

        return back()->with('message', 'We have e-mailed your password reset link!');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function showResetPasswordForm($token)
    {
        $updatePassword = DB::table('password_resets')
            ->where([
                // 'email' => $request->email,
                'token' => $token
            ])->first();

        //if Password update is fails
        if (!$updatePassword) {
            return redirect('/login')->with('message', 'Invalid token!');
        }
        $a = Carbon::parse($updatePassword->created_at)->addMinutes(5);
        $b = $a->toDateTimeString();
        if (Carbon::now()->toDateTimeString() > $b) {
            DB::table('password_resets')->where('email', $updatePassword->email)->delete();
            return redirect('/login')->with('message', 'Token expired');
        }
        // else{
        //     dd("false");
        // }

        $email = DB::table('password_resets')->first();
        return view('resetpassword.forgetPasswordLink', ['token' => $token, 'email' => $email]);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function submitResetPasswordForm(Request $request)
    {
        // return $request->all();
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password'
        ]);
        // return $request->all();
        //         $updatePassword = DB::table('password_resets')
        //         ->where([
        //             'email' => $request->email,
        //             'token' => $request->token
        //         ])->first();

        // // return response()->json($updatePassword);
        //         //if Password update is fails
        //         if (!$updatePassword) {
        //             // return $request->all();
        //             return back()->withInput()->with('error', 'Invalid token!');
        //         }

        //         if ($updatePassword->created_at > Carbon::parse($updatePassword->created_at)->addseconds(5)) {
        //             $updatePassword->delete();//!dont run
        //             // DB::table('password_resets')->where('email', $request->email)->delete();
        //             // return response(['message' => 'Token has expired'], 422);
        //             return redirect('/login')->with('message', 'Token expired');
        //         }

        //         else{
        //             //if Password update is success then saved in users rows
        $user = User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        //Checkinig condition where password updated token is deleted
        if ($user) {
            DB::table('password_resets')->where('email', $request->email)->delete();
            // DB::table('password_resets')->where('email', $updatePassword->email)->delete();

            return redirect('/login')->with('message', 'Your password has been changed!');
        }
        // }
    }
}
