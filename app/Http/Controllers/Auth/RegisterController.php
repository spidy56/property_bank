<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::PROPERTY;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', Rule::unique('users')->where(function ($query) {
                return $query->where('is_active', 'Active');
            })],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->where(function ($query) {
                return $query->where('is_active', 'Active');
            })],
            'contact_no' => ['required', 'string', 'max:10'],
            'password' => ['required', 'string', 'min:8', 'confirmed']
            // 'username' => ['required', 'string', 'unique:users'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([            
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            // 'aadhar_card_no' => $data['aadhar_card_no'],
            'contact_no' => $data['contact_no'],
            'password' => Hash::make($data['password']),
        ]);

        $associateID = "PBAS".str_pad($user->id, 4, '0', STR_PAD_LEFT);

        $user->where("id", $user->id)->update(['associate_id' => $associateID ]);

        return $user;
    }
}
