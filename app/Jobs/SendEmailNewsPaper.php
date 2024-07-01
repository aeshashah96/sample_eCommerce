<?php

namespace App\Jobs;

use App\Mail\SendEmailNewsPaper as MailSendEmailNewsPaper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailNewsPaper implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $newspaper;
    /**
     * Create a new job instance.
     */
    public function __construct($newspaper)
    {
        $this->newspaper = $newspaper;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $mail = new MailSendEmailNewsPaper($this->newspaper);
        Mail::to($this->newspaper->email)->send($mail);
        \Log::info("mail Send Successfully for News Paper User");
    }
}
