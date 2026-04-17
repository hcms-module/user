<?php

declare(strict_types=1);

namespace App\Application\User\Controller;

use App\Annotation\Api;
use App\Annotation\View;
use App\Application\Admin\Middleware\AdminMiddleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\Session\Middleware\SessionMiddleware;
use App\Application\Admin\Service\AdminUserService;
use App\Application\User\Model\User;
use App\Application\User\Model\UserVip;
use App\Application\User\Service\UserVipService;
use App\Controller\AbstractController;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;

#[Middlewares([SessionMiddleware::class, AdminMiddleware::class])]
#[Controller("user/user")]
class UserController extends AbstractController
{
    #[Inject]
    protected UserVipService $userVipService;

    #[Api]
    #[PostMapping("index/status")]
    public function status()
    {
        $user_vip_id = (int)$this->request->input('user_vip_id', 0);
        $status = (int)$this->request->input('status', 1);
        $user_vip = UserVip::find($user_vip_id);
        if (!$user_vip) {
            return $this->returnErrorJson('找不到该记录');
        }
        $user_vip->status = $status;

        return $user_vip->save() ? [] : $this->returnErrorJson();
    }

    #[Api]
    #[PostMapping("index/renewal")]
    public function renewalSubmit()
    {
        $user_id = (int)$this->request->input('user_id', 0);
        $vip_type = (int)$this->request->input('vip_type', 0);
        $expire_day = (int)$this->request->input('expire_day', 0);
        if ($expire_day === 0) {
            return $this->returnErrorJson('变动时间不能为0');
        }
        $res = $this->userVipService->renewalByExpireDay($user_id, $expire_day, $vip_type,
            intval(AdminUserService::getInstance()
                    ->getAdminUserId() . time()), 'admin');

        return $res ? [] : $this->returnErrorJson();
    }

    #[Api]
    #[GetMapping("index/lists")]
    public function lists()
    {
        $where = [];
        $username = trim($this->request->input('username', ''));
        $vip_type = intval($this->request->input('vip_type', 0));

        $share_user_id = (int)$this->request->input('share_user_id', 0);

        if ($username) {
            $where[] = ['username', 'like', "%{$username}%"];
        }

        if ($share_user_id > 0) {
            $where[] = ['share_user_id', '=', $share_user_id];
        }

        $builder = User::where($where)
            ->with(['vips'])
            ->orderByDesc('user_id');
        if ($vip_type > 0) {
            $vip_user_ids = UserVip::where('vip_type', $vip_type)
                ->pluck('user_id')
                ->toArray();
            $builder->whereIn('user_id', empty($vip_user_ids) ? [0] : $vip_user_ids);
        }
        $lists = $builder->paginate();

        $vip_types = UserVip::VIP_TYPES;

        return compact('lists', 'vip_types');
    }

    #[View]
    #[GetMapping]
    public function index()
    {
    }
}
