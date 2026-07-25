<?php

namespace App\Services;

use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Database\Eloquent\Collection;

class AdminUserService
{
    public function listAllOrdered(): Collection
    {
        return User::orderBy('role')->orderBy('name')->get();
    }

    public function create(array $data): User
    {
        $user = User::create($data);
        AuditLogger::record('user.create', $user);

        return $user;
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);
        AuditLogger::record('user.update', $user);

        return $user;
    }

    public function delete(User $user): void
    {
        AuditLogger::record('user.delete', $user);
        $user->delete();
    }

    public function toggleActive(User $user): User
    {
        $user->update(['is_active' => ! $user->is_active]);
        AuditLogger::record('user.toggle', $user);

        return $user;
    }
}
