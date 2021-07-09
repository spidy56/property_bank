<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class sendGrid extends Mailable
{
    use Queueable, SerializesModels;

    public $input;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($input)
    {
        $this->input = $input;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    // public function build()
    // {
    //     // return $this->markdown('emails.sendGrid');
    //     return $this->markdown('emails.mailBlade')
    //     ->with([
    //         'messages' => $this->input['messages'],
    //         'title' => $this->input['subject'],
    //     ])
    //     ->from('bhavesh.programmics@gmail.com', 'Bhavesh Verma')
    //     ->subject("Crawling Details");
    // }

    public function build()
    {
        return $this->markdown('emails.mailBlade')
        ->with([
            'email' => $this->input['email'],
            'token' => $this->input['token'],
            'link' => $this->input['link'],
        ])
        ->from('noreplypropertybird@gmail.com', 'Property Bank - Reset Password')
        ->subject("Property Bank - Reset Password");
    }
}
