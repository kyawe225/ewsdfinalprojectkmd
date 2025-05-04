<?php

namespace App\Notifications;

use App\Mail\ReallocationTutorMail;
use App\Models\Student;
use App\Models\Tutor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReallocatedTutor extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private Student $student,private Tutor $tutor)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): Mailable
    {
        return (new ReallocationTutorMail($this->student, $this->tutor))->to($this->tutor->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            "student_id" => $this->student->id,
            "tutor_id"=>$this->tutor->id,
            "message"=>"allocated student a students"
        ];
    }
}
