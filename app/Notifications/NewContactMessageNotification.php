<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ContactMessage $contactMessage) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Contact Form Inquiry: {$this->contactMessage->subject}")
            ->greeting("Hello {$notifiable->name},")
            ->line("You received a new contact message from {$this->contactMessage->name} ({$this->contactMessage->email}).")
            ->line("Subject: {$this->contactMessage->subject}")
            ->line("Message:")
            ->line($this->contactMessage->message)
            ->action('View Messages in Admin', url('/admin/messages'));
    }
}
