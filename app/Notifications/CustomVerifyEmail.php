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

    /**
     * Create a new notification instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }



//    protected function verificationUrl($notifiable)
//    {
//        return url('/api/verify-email?token=' . $notifiable->email_verification_token);
//    }
//    public function toMail($notifiable)
//    {
//
//        return (new MailMessage)
//            ->subject('Verify Your Email Address')
//            ->line('Please click the button below to verify your email address.')
//            ->action('Verify Email Address', $this->verificationUrl($notifiable))
//            ->line('If you did not create an account, no further action is required.');
//    }

    protected function verificationUrl()
    {
        // Generate a unique token (you can use any token generation logic here)
        $token = bin2hex(random_bytes(32));

        // Set the expiration timestamp (e.g., 60 minutes from now)
        $expiresAt = Carbon::now()->addMinutes(180);
        echo $expiresAt;

        // Store the hashed token in the database (assuming you have an email_verification_token column)
        $this->user->email_verification_token = $token;
        $token = Hash::make($token);
        $this->user->hashed_email_verification_token = $token;
        $this->user->email_verification_token_expires_at = $expiresAt->toDateTimeString(); // Convert to MySQL datetime format
        $this->user->save();

        // Create the custom verification URL with token and expiration timestamp as query parameters
        $url = config('app.url') . "/api/verify-email?token={$token}&expires_at={$expiresAt}";

        return $url;
    }

    /**
     * Build the mails representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(): MailMessage
    {

       $url = $this->verificationUrl();
       return (new MailMessage)
           ->subject('Verify Your Email Address')
           ->line('Please click the button below to verify your email address.')
           ->action('Verify Email Address', $url)
           ->line('If you did not create an account, no further action is required.')
           ->line('If you’re having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your web browser: ' . $url);

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
