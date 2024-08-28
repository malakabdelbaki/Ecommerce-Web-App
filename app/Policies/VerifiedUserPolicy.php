<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class VerifiedUserPolicy
{
    public function access(User $user)
    {
        return $user->hasVerifiedEmail();
    }
}
