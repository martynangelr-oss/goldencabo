<?php

namespace App\Services;

use App\Models\Contact;
use App\Support\AuditLogger;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContactService
{
    public function create(array $data): Contact
    {
        $contact = Contact::create($data);
        AuditLogger::record('contact.create', $contact);

        return $contact;
    }

    public function paginateForAdmin(int $perPage = 20): LengthAwarePaginator
    {
        return Contact::latest()->paginate($perPage);
    }

    public function markAllRead(): void
    {
        Contact::where('read', false)->update(['read' => true]);
    }

    public function delete(Contact $contact): void
    {
        AuditLogger::record('contact.delete', $contact);
        $contact->delete();
    }

    public function countTotal(): int
    {
        return Contact::count();
    }

    public function countUnread(): int
    {
        return Contact::where('read', false)->count();
    }
}
