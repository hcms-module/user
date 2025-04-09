<?php

namespace App\Application\User\Listener;

use App\Application\User\Event\UserLoginSuccessEvent;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;

#[Listener]
class UserLoginListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            UserLoginSuccessEvent::class
        ];
    }

    public function process(object $event): void
    {
        if ($event instanceof UserLoginSuccessEvent) {
        }
    }
}