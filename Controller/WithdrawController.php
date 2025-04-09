<?php

declare(strict_types=1);

namespace App\Application\User\Controller;


use App\Annotation\Api;
use App\Annotation\View;
use App\Application\Admin\Middleware\AdminMiddleware;
use App\Application\Keepa\Model\KeepaShareWithdraw;
use App\Application\Keepa\Model\KeepaUser;
use App\Application\User\Model\ShareWithdraw;
use App\Application\User\Model\User;
use App\Application\User\Service\ShareRewardService;
use App\Application\User\Service\ShareWithdrawService;
use App\Controller\AbstractController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;


/**
 * @Middleware(AdminMiddleware::class)
 * @Controller(prefix="/user/withdraw")
 */
#[Middleware(AdminMiddleware::class)]
#[Controller("/user/withdraw")]
class WithdrawController extends AbstractController
{


    /**
     * @Api()
     * @PostMapping("index/reject")
     */
    #[Api]
    #[PostMapping("index/reject")]
    public function withdrawReject()
    {
        $withdraw_id = intval($this->request->input('withdraw_id', 0));
        $withdraw = ShareWithdraw::find($withdraw_id);
        if (!$withdraw) {
            return $this->returnErrorJson('找不到该记录');
        }
        $withdraw_service = new ShareWithdrawService();

        return $withdraw_service->reject($withdraw_id, "拒绝打款") ? [] : $this->returnErrorJson();
    }

    /**
     * @Api()
     * @PostMapping("index/agree")
     */
    #[Api]
    #[PostMapping("index/agree")]
    public function withdrawAgree()
    {
        $withdraw_id = intval($this->request->input('withdraw_id', 0));
        $withdraw = ShareWithdraw::find($withdraw_id);
        if (!$withdraw) {
            return $this->returnErrorJson('找不到该记录');
        }
        $withdraw_service = new ShareWithdrawService();

        return $withdraw_service->pay($withdraw_id) ? [] : $this->returnErrorJson();
    }

    /**
     * @PostMapping(path="withdraw")
     */
    #[Api]
    #[PostMapping("withdraw")]
    public function withdraw()
    {
        $validator = $this->validationFactory->make($this->request->all(), [
            'real_name' => 'required',
            'account' => 'required',
            'withdraw_amount' => 'required',
        ], [
            'withdraw_amount.required' => '请输入提现金额',
            'account.required' => '账户不能为空',
            'real_name.required' => '请输入账户姓名',
        ]);
        if ($validator->fails()) {
            return $this->returnErrorJson($validator->errors()
                ->first());
        }

        $withdraw_amount = (float)$this->request->input('withdraw_amount', 0);
        $withdraw_type = (int)$this->request->input('withdraw_type', KeepaShareWithdraw::TYPE_ALIPAY);
        $real_name = $this->request->input('real_name');
        $account = $this->request->input('account');
        $user_id = (new KeepaUser())->getLoginUserId();

        $max_withdraw_amount = $this->setting->getKeepaSetting('max_withdraw_amount', 100);
        if ($withdraw_amount < $max_withdraw_amount) {
            return $this->returnErrorJson('提现金额不能低于' . $max_withdraw_amount);
        }

        $withdraw_service = new \App\Application\Keepa\Service\ShareWithdrawService();
        $res = $withdraw_service->createWithdraw($withdraw_amount, $user_id, $withdraw_type, $real_name, $account);

        return $res ? [] : $this->returnErrorJson();
    }


    /**
     * @GetMapping(path="withdrawInfo")
     */
    #[Api]
    #[GetMapping("withdrawInfo")]
    public function withdrawInfo()
    {
        $user_id = (new User())->getLoginUserId();
        $can_withdraw = ShareRewardService::getUserBalance($user_id, true);
        $where = [
            ['user_id', '=', $user_id]
        ];
        $lists = ShareWithdraw::where($where)
            ->orderByDesc('created_at')
            ->paginate();

        //找出最后一次提交的记录
        $last_withdraw = ShareWithdraw::where('user_id', $user_id)
            ->orderByDesc('created_at')
            ->first();

        return compact('can_withdraw', 'lists', 'last_withdraw');
    }


    /**
     * @Api()
     * @GetMapping("index/lists")
     */
    #[Api]
    #[GetMapping("index/lists")]
    public function withdrawList()
    {
        $where = [];
        $lists = ShareWithdraw::where($where)
            ->orderByDesc('withdraw_id')
            ->paginate();

        return compact('lists');
    }

    /**
     * @View()
     * @GetMapping(path="index")
     */
    #[View]
    #[GetMapping]
    public function index()
    {
    }
}
