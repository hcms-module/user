<?php

declare(strict_types=1);

namespace App\Application\User\Service;

use App\Application\User\Event\UserRenewalSuccessEvent;
use App\Application\User\Model\UserVip;
use App\Application\User\Model\UserVipRecord;
use App\Exception\ErrorException;
use Hyperf\Di\Annotation\Inject;
use Psr\EventDispatcher\EventDispatcherInterface;

class UserVipService
{

    #[Inject]
    protected EventDispatcherInterface $eventDispatcher;

    /**
     * 会员是否vip
     *
     * @param int $userId
     * @param int $vip_type
     * @return bool
     */
    public function isVip(int $userId, int $vip_type = UserVip::VIP_TYPE_DEFAULT): bool
    {
        return UserVip::where('user_id', $userId)
                ->where('vip_type', $vip_type)
                ->where('expire_time', '>', time())
                ->where('status', UserVip::STATUS_VALID)
                ->count() > 0;
    }


    /**
     * 获取用户VIP对象
     *
     * @param int $userId
     * @param int $vip_type
     * @return UserVip|null
     */
    public function getVip(int $userId, int $vip_type = UserVip::VIP_TYPE_DEFAULT): ?UserVip
    {
        return UserVip::where('user_id', $userId)
            ->where('vip_type', $vip_type)
            ->where('expire_time', '>', time())
            ->first();
    }

    /**
     * @throws ErrorException
     */
    public function renewalByExpireDay(
        int $userId,
        int $expire_day,
        int $vip_type = UserVip::VIP_TYPE_DEFAULT,
        int $target = 0,
        $target_type = ''
    ): UserVip {
        $vip = $this->getVip($userId, $vip_type);
        if (!$vip) {
            $expire_time = time() + $expire_day * 86400;
        } else {
            $expire_time = max(time(), $vip->expire_time) + $expire_day * 86400;
        }

        return $this->renewal($userId, $expire_time, $vip_type, $target, $target_type);
    }

    public function renewal(
        int $userId,
        int $expire_time,
        int $vip_type = UserVip::VIP_TYPE_DEFAULT,
        int $target = 0,
        $target_type = ''
    ): UserVip {
        $user_vip = UserVip::firstOrNew(['user_id' => $userId, 'vip_type' => $vip_type]);
        $origin_expire_time = time();
        if ($user_vip->user_vip_id > 0) {
            //已经存在记录
            $origin_expire_time = $user_vip->expire_time;
            $user_vip->expire_time = $expire_time;
        } else {
            //未存在
            $user_vip->expire_time = $expire_time;
        }
        $res = $user_vip->save();
        if ($res) {
            //创建续费记录
            $record = UserVipRecord::create([
                'user_id' => $userId,
                'user_vip_id' => $user_vip->user_vip_id,
                'new_expire_time' => $expire_time,
                'origin_expire_time' => $origin_expire_time,
                'change_time' => $expire_time - $origin_expire_time,
                'target' => $target,
                'target_type' => $target_type
            ]);
            if (!$record) {
                throw new ErrorException('创建续费记录失败');
            }
            //续费成功，触发事件
            $this->eventDispatcher->dispatch(new UserRenewalSuccessEvent($user_vip, $record));
        }

        return $user_vip;
    }
}
