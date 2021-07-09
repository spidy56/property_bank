<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
	use AuthenticatesUsers;

    protected function authenticated(Request $request, $user)
    {
        return redirect()->route('admin.properties');
    }


    public function register(Request $request)
    {
        // return redirect()->route('admin.register');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        return redirect()->route('admin.login');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('admin');
    }

    public function submitAdminPassword(Request $req)
    {
        $admin = Admin::find($req->id);

        $admin->password = Hash::make($req->password);

        $query = $admin->save();

        if ($query)
        {
            return response()->json([
                "status" => true,
                'message' => 'Your password reset successfully !'
            ]);
        }
        else
        {
            return response()->json([
                "status" => false,
                'message' => 'Something went wrong !'
            ]);
        }
    }
}
