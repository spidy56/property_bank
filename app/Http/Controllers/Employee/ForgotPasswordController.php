<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use DB;
use App\Http\Controllers\Employee\MailController;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    public function postEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return back()->withErrors(['email' => 'Please enter an email !']);
        }
        else
        {
            $check = User::where("email", $request->email)->value("id") ? User::where("email", $request->email)->value("id") : 0;
            if ($check > 0)
            {
                $token = Str::random(64);

                $dateTime = date("Y-m-d H:i:s");

                $find = DB::table('password_resets')->where("user_id", $check)->where("is_active", "Yes")->first();

                if ($find)
                {
                    $dataUpdate = [
                        'is_active' => "No",
                    ];
                    DB::table('password_resets')->where("user_id", $check)->update($dataUpdate);
                }

                $data = [
                    'user_id' => $check,
                    'email' => $request->email,
                    'token' => $token,
                    'created_at' => $dateTime
                ];

                $insert = DB::table('password_resets')->insertGetId($data);

                $link = url('associate/password/reset').'/'.$token;

                $output = [
                    'email' => $request->email,
                    'token' => $token,
                    'link' => $link
                ];

                if ($insert)
                {
                    $send = new MailController();
                    $sended = $send->sendMail($output);
                    // dd($sended);

                    if ($sended)
                    {
                        return back()->with('success', 'We have e-mailed your password reset link !');
                    }
                    else
                    {
                        return back()->with('error', 'E-mail sending failed !');
                    }

                }
                else
                {
                    return back()->with('error', 'Something went wrong !');
                }
            }
            else
            {
                return back()->withErrors(['email' => 'Email address not exists !']);
            }
        }   
    }

    public function show_reset(Request $request)
    {
        $token = $request->token;
        $data = DB::table('password_resets')->where('is_active', 'Yes')->where('token', $token)->get()->count();
        // dd($data);
        if ($data > 0)
        {
            return view('auth.passwords.reset')->with(['token' => $token ]);
        }
        else
        {
            return abort('419');
        }
        
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput($request->only('password'))->withErrors(['password' => ' Both password fields are required, must be same, and min length : 8 ']);
        }
        else
        {
            $dateTime = date("Y-m-d H:i:s");

            $find = DB::table('password_resets')->where("token", $request->token)->where("is_active", "Yes")->first();

            if ($find)
            {
                $id = User::where("email", $find->email)->value("id");

                $user = User::find($id);
                $user->password = Hash::make($request->password);
                $updated = $user->save();
                if ($updated) {

                    $dataUpdate = [
                        'is_active' => "No",
                    ];
                    DB::table('password_resets')->where("id", $find->id)->update($dataUpdate);

                    // return back()->with('success', 'Password reset successfully !');
                    return redirect('login')->with('success', 'Password reset successfully !');
                }
                else
                {
                    return back()->with('error', 'Something went wrong !');
                }
            }
            else
            {
                return back()->with('error', 'Something went wrong !');
            }

        }
    }
}
