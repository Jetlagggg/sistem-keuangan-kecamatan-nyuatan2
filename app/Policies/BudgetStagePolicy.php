<?php

namespace App\Policies;

use App\Models\BudgetStage;
use App\Models\User;

class BudgetStagePolicy
{
    public function create(User $user): bool
    {
        return $user->canManage();
    }

    public function delete(User $user, BudgetStage $budgetStage): bool
    {
        return $user->isAdmin();
    }
}
