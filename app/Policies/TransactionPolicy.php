<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->canManage();
    }

    public function update(User $user, Transaction $transaction): bool
    {
        return $user->canManage();
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Transaction $transaction): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Transaction $transaction): bool
    {
        return $user->isAdmin();
    }
}
