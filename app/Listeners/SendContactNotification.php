<?php

namespace App\Listeners;

use App\Events\ContactMessageReceived;
use App\Models\User;
use App\Notifications\NewContactMessageNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendContactNotification implements ShouldQueue
{
    public function handle(ContactMessageReceived $event): void
    {
        $admin = User::first();
        if ($admin) {
            $admin->notify(new NewContactMessageNotification($event->message));
        }
    }
}
