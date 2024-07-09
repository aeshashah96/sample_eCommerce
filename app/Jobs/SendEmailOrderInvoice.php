<?php

namespace App\Jobs;

use App\Mail\OrderInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailOrderInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $order,$totalItem,$user,$productName,$productPrice;
    /**
     * Create a new job instance.
     */
    public function __construct($order,$totalItem,$user,$productName,$productPrice)
    {
        $this->order = $order;
        $this->totalItem = $totalItem;
        $this->user = $user;
        $this->productName = $productName;
        $this->productPrice = $productPrice;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
    
        $email = new OrderInvoice($this->order,$this->totalItem,$this->user,$this->productName,$this->productPrice);
        // ($this->user->email);
        \Log::info($this->user->email);
        Mail::to($this->user->email)->send($email);
        // Log::info("Order Invoice mail Send Successfully");
    }
}
