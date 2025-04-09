<?php

namespace App\Application\User\Event;

use App\Application\User\Model\UserLoginToken;

class UserLoginSuccessEvent
{

    public function __construct(protected UserLoginToken $userLoginToken) { }

    public function getUserLoginToken(): UserLoginToken
    {
        return $this->userLoginToken;
    }

}