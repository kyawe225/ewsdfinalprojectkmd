<?php

namespace App\Jobs;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Notifications\Notifiable;
use Log;
use Notification;

class SendEmailNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private $notification, private $notifiableObj)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try{
            $this->notifiableObj->notify($this->notification);
        }catch(Exception $e){
            Log::debug($e->getMessage());
        }
    }
}
