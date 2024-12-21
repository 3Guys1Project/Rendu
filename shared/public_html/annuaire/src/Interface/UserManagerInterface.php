<?php

namespace App\Interface;

use App\Entity\User;

interface UserManagerInterface
{
    public function processNewUser(User $user, ?string $plainPassword): void;
}