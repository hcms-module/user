<?php

namespace App\Application\User\Event;

use App\Application\User\Model\UserVip;
use App\Application\User\Model\UserVipRecord;

class UserRenewalSuccessEvent
{
    public function __construct(protected UserVip $userVip, protected UserVipRecord $userVipRecord)
    {
    }

    public function getUserVip(): UserVip
    {
        return $this->userVip;
    }

    public function getUserVipRecord(): UserVipRecord
    {
        return $this->userVipRecord;
    }
}