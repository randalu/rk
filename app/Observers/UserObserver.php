<?php

namespace App\Observers;

use App\Models\User;

class UserObserver extends BaseObserver
{
    public function created(User $user): void
    {
        $this->log($user, 'created', null, [
            'name'    => $user->name,
            'email'   => $user->email,
            'role_id' => $user->role_id,
        ]);
    }

    public function updated(User $user): void
    {
        // Never log password changes
        $dirty = collect($user->getDirty())
            ->except(['password', 'remember_token'])
            ->toArray();

        if (empty($dirty)) return;

        $this->log($user, 'updated',
            collect($user->getOriginal())->except(['password', 'remember_token'])->toArray(),
            $dirty
        );
    }

    public function deleted(User $user): void
    {
        $this->log($user, 'deleted', ['name' => $user->name]);
    }
}