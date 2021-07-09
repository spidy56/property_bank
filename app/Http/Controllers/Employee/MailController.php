<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use Validator;
use App\Mail\sendGrid;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function sendMail($output)
    {
        Mail::to($output['email'])->send(new sendGrid($output));
        return true;
    }

    // public function sendMail($uniqueVals)
    // {
    //     Mail::to('vickybaghel333@gmail.com')->send(new SendGrid());
    //     // dd('Mail Sent');
    // }
}