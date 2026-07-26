<?php

namespace App\Services;

use App\Events\ContactMessageReceived;
use App\Models\ContactMessage;

class ContactService
{
    public function getMessages(array $filters = [])
    {
        $query = ContactMessage::query()->orderBy('created_at', 'desc');

        if (isset($filters['is_read'])) {
            $isRead = filter_var($filters['is_read'], FILTER_VALIDATE_BOOLEAN);
            $query->where('is_read', $isRead);
        }

        if (isset($filters['archived'])) {
            $archived = filter_var($filters['archived'], FILTER_VALIDATE_BOOLEAN);
            $query->where('archived', $archived);
        }

        if (!empty($filters['per_page'])) {
            return $query->paginate((int) $filters['per_page']);
        }

        return $query->get();
    }

    public function createMessage(array $data): ContactMessage
    {
        $message = ContactMessage::create($data);

        event(new ContactMessageReceived($message));

        return $message;
    }

    public function markAsRead(ContactMessage $message): ContactMessage
    {
        $message->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return $message->fresh();
    }

    public function deleteMessage(ContactMessage $message): bool
    {
        return (bool) $message->delete();
    }
}
