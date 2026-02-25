<?php

namespace App\Observers;

use App\Models\AdminNotification;
use App\Models\ContactMessage;

class ContactMessageObserver
{
    public function created(ContactMessage $contact)
    {
        AdminNotification::createIfEnabled('contacts', [
            'title' => 'رسالة تواصل جديدة',
            'message' => "رسالة من {$contact->name}: ".\Str::limit($contact->subject, 50),
            'icon' => 'fas fa-envelope',
            'color' => 'warning',
            'link' => route('admin.contact-messages.show', $contact->id),
            'related_id' => $contact->id,
            'related_type' => ContactMessage::class,
        ]);
    }
}
