<?php

namespace App\Notifications;

use Fisal\Otp\Otp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendOtpNotify extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $otp;

    public function __construct()
    {
        $this->otp = new Otp();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [\App\Channels\WhatsappChannel::class];
    }

    /**
     * Get the WhatsApp representation of the notification.
     * Used by WhatsappChannel to send OTP via beon.chat
     */
    public function toBeon(object $notifiable): array
    {
        $otp = $this->otp->generate($notifiable->phone, 'numeric', 5, 20);

        return [
            'phone' => $notifiable->phone,
            'code'  => $otp->token,
            'name'  => $notifiable->name ?? 'User',
            'type'  => 'sms',
            'lang'  => 'ar',
        ];
    }

    /**
     * Get the mail representation of the notification.
     * Fallback method (optional)
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->greeting('OTP Code')
                    ->line('Verify your code.')
                    ->line('Use the code sent to your phone.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
