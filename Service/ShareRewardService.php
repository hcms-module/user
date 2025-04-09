<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/10/29 14:53
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\User\Service;


use App\Application\User\Model\UserShareReward;
use App\Application\User\Model\ShareWithdraw;
use App\Exception\ErrorException;

class ShareRewardService
{
    /**
     * @param ShareWithdraw $withdraw
     * @return UserShareReward
     * @throws ErrorException
     */
    public function payWithdraw(ShareWithdraw $withdraw): UserShareReward
    {
        $reward_amount = -abs(floatval($withdraw->withdraw_amount));
        $description = '提现';

        return self::createReword($withdraw->user_id, $withdraw->withdraw_id . '', 'withdraw', $reward_amount,
            $description);
    }

    /**
     * 创建记录
     *
     * @param int    $user_id
     * @param string $target
     * @param string $target_type
     * @param float  $reward_amount
     * @param string $description
     * @return UserShareReward
     * @throws ErrorException
     */
    public function createReword(
        int $user_id,
        string $target,
        string $target_type,
        float $reward_amount,
        string $description = ''
    ): UserShareReward {
        //如果target 和 target_type 一致，则代表重复创建
        $is_exist = UserShareReward::where('target', $target)
                ->lock()
                ->where('target_type', $target_type)
                ->count() > 0;
        if ($is_exist) {
            throw new ErrorException('重复创建记录');
        }
        $data = [
                'balance' => self::getUserBalance($user_id) + $reward_amount,

            ] + compact('user_id', 'target', 'target_type', 'reward_amount', 'description');

        return UserShareReward::create($data);
    }

    /**
     * 获取用户余额
     *
     * @param int  $user_id
     * @param bool $can_withdraw
     * @return float
     */
    public static function getUserBalance(int $user_id, bool $can_withdraw = false): float
    {
        $reward_amount = (new UserShareReward())->where('user_id', $user_id)
            ->sum('reward_amount');
        $reward_amount = floatval(sprintf("%0.2f", $reward_amount));
        if ($can_withdraw) {
            //获取正在提现的金额
            $withdraw_amount = ShareWithdraw::where('user_id', $user_id)
                ->whereIn('status', [
                    ShareWithdraw::STATUS_PENDING,
                    ShareWithdraw::STATUS_SUCCESS,
                ])
                ->sum('withdraw_amount');
            $withdraw_amount = floatval(sprintf("%0.2f", $withdraw_amount));
            $reward_amount -= $withdraw_amount;
        }

        return $reward_amount;
    }

    /**
     * 获取用户累计的总收益
     *
     * @param int $user_id
     * @return float
     */
    public static function getTotal(int $user_id): float
    {
        $reward_amount = (new UserShareReward())->where('user_id', $user_id)
            ->where('reward_amount', '>', 0)
            ->sum('reward_amount');

        return floatval(sprintf("%0.2f", $reward_amount));
    }
}