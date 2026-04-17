<?php

declare(strict_types=1);

namespace App\Application\User\Controller;


use App\Annotation\Api;
use App\Annotation\View;
use App\Application\Admin\Middleware\AdminMiddleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\Session\Middleware\SessionMiddleware;
use App\Application\User\Model\ShareWithdraw;
use App\Application\User\Model\User;
use App\Application\User\Service\ShareWithdrawService;
use App\Controller\AbstractController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;

#[Middlewares([SessionMiddleware::class, AdminMiddleware::class])]
#[Controller("/user/withdraw")]
class WithdrawController extends AbstractController
{


    #[Api]
    #[PostMapping("index/reject")]
    public function withdrawReject()
    {
        $withdraw_id = intval($this->request->input('withdraw_id', 0));
        $reject_msg = trim($this->request->input('reject_msg', ''));
        $withdraw = ShareWithdraw::find($withdraw_id);
        if ($reject_msg == '') {
            return $this->returnErrorJson('请输入拒绝理由');
        }
        if (!$withdraw) {
            return $this->returnErrorJson('找不到该记录');
        }
        $withdraw_service = new ShareWithdrawService();

        return $withdraw_service->reject($withdraw_id, $reject_msg) ? [] : $this->returnErrorJson();
    }

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

    #[Api]
    #[GetMapping("index/lists")]
    public function withdrawList()
    {
        $where = [];
        $username = trim($this->request->input('username', ''));
        $real_name = trim($this->request->input('real_name', ''));
        $builder = ShareWithdraw::where($where)
            ->with(['user'])
            ->orderByDesc('withdraw_id');
        if ($username != '') {
            $user_ids = User::where('username', 'like', '%' . $username . '%')
                ->pluck('user_id')
                ->toArray() ?: [0];
            $builder->whereIn('user_id', $user_ids);
        }
        if ($real_name != '') {
            $builder->where('real_name', 'like', '%' . $real_name . '%');
        }
        $lists = $builder->paginate();

        return compact('lists');
    }

    #[View]
    #[GetMapping]
    public function index()
    {
    }
}
