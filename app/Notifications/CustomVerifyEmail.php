<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;


class CustomVerifyEmail extends Notification
{
    use Queueable;
    protected User $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    protected function verificationUrl()
    {
        $token = bin2hex(random_bytes(32));
        $expiresAt = Carbon::now()->addMinutes(30);
        $this->user->email_verification_token = hash('sha256',$token);
        $this->user->email_verification_token_expires_at = $expiresAt->toDateTimeString(); // Convert to MySQL datetime format
        $this->user->save();
        $url = config('app.url') . "/api/verify-email?token={$token}&expires_at={$expiresAt}";

        return $url;
    }

    public function toMail(): MailMessage
    {
       $url = $this->verificationUrl();
       return (new MailMessage)
           ->markdown('vendor.notifications.email')
           ->subject('Verify Your Email Address')
           ->line('Please click the button below to verify your email address.')
           ->action('Verify Email Address', $url)
           ->line('If you did not create an account with us, no further action is required.');

    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }


}
