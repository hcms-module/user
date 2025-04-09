<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/10/30 11:38
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\User\Service;

use App\Application\User\Model\ShareWithdraw;
use App\Exception\ErrorException;
use Hyperf\DbConnection\Db;

class ShareWithdrawService
{

    /**
     *
     * @param int    $withdraw_id
     * @param string $reject_msg
     * @return bool
     * @throws ErrorException
     */
    public function cancel(int $withdraw_id, string $reject_msg = ''): bool
    {
        $withdraw = ShareWithdraw::find($withdraw_id);
        if ($withdraw != ShareWithdraw::STATUS_PENDING) {
            throw new ErrorException('该提现状态不支持取消');
        }
        $withdraw->status = ShareWithdraw::STATUS_CANCEL; //取消
        $withdraw->reject_msg = $reject_msg;

        return $withdraw->save();
    }


    /**
     * 拒绝审核
     *
     * @param int    $withdraw_id
     * @param string $reject_msg
     * @return bool
     * @throws ErrorException
     */
    public function reject(int $withdraw_id, string $reject_msg): bool
    {
        $withdraw = ShareWithdraw::find($withdraw_id);
        if ($withdraw != ShareWithdraw::STATUS_PENDING) {
            throw new ErrorException('该提现状态不支持操作');
        }
        $withdraw->status = ShareWithdraw::STATUS_REJECT; //拒绝打款
        $withdraw->reject_msg = $reject_msg;

        return $withdraw->save();
    }

    /**
     * 提现完成打款
     *
     * @param int $withdraw_id
     * @return bool
     * @throws ErrorException
     */
    public function pay(int $withdraw_id): bool
    {
        $withdraw = ShareWithdraw::find($withdraw_id);
        if ($withdraw->status != ShareWithdraw::STATUS_PENDING || $withdraw->status != ShareWithdraw::STATUS_SUCCESS) {
            $withdraw->status = ShareWithdraw::STATUS_REMIT; //完成打款
            Db::beginTransaction();
            if ($withdraw->save()) {
                //创建记录
                if ((new ShareRewardService())->payWithdraw($withdraw)->reward_id > 0) {
                    Db::commit();

                    return true;
                }
            }
            Db::rollBack();

            return false;
        }
        throw new ErrorException('该提现状态不支持操作');
    }

    /**
     * 提现申请成功通过审核
     *
     * @param int $withdraw_id
     * @return bool
     */
    public function success(int $withdraw_id): bool
    {
        $withdraw = ShareWithdraw::find($withdraw_id);
        $withdraw->status = ShareWithdraw::STATUS_SUCCESS; //审核通过

        return $withdraw->save();
    }

    /**
     * @param float  $amount
     * @param int    $user_id
     * @param int    $type
     * @param string $real_name
     * @param string $account
     * @return bool
     * @throws ErrorException
     */
    public function createWithdraw(
        float $amount,
        int $user_id,
        int $type = ShareWithdraw::TYPE_ALIPAY,
        string $real_name = '',
        string $account = ''
    ): bool {
        //获取用户当前可提现月
        $balance = ShareRewardService::getUserBalance($user_id);
        if ($amount <= 0) {
            throw new ErrorException('提现金额必须大于0');
        }
        if ($balance < $amount) {
            throw new ErrorException('可提现余额不足');
        }
        $withdraw = ShareWithdraw::create([
            'withdraw_amount' => $amount,
            'user_id' => $user_id,
            'withdraw_type' => $type,
            'real_name' => $real_name,
            'account' => $account,
            'status' => ShareWithdraw::STATUS_PENDING
        ]);

        //TODO 这里可以监听一个提现事件
        return true;
    }
}