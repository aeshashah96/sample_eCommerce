<?php

namespace App\Jobs;

use App\Mail\ContactUsUserMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailContactUsUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $contact;
    /**
     * Create a new job instance.
     */
    public function __construct($contact)
    {
        $this->contact = $contact;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $email = new ContactUsUserMail($this->contact);
        Mail::to($this->contact->email)->send($email);
        Log::info("mail Send Successfully");
    }
}
