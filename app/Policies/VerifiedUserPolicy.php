<?php

namespace App\Policies;

use App\Models\User;

class VerifiedUserPolicy
{
    public function access(User $user)
    {
        return $user->hasVerifiedEmail();
    }
}
