<?php

declare(strict_types=1);

namespace App\Application\User\Controller;


use App\Annotation\Api;
use App\Annotation\View;
use App\Application\Admin\Middleware\AdminMiddleware;
use App\Application\User\Model\UserShareReward;
use App\Application\User\Model\User;
use App\Controller\AbstractController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;

#[Middleware(AdminMiddleware::class)]
#[Controller("user/sharereward")]
class ShareRewardController extends AbstractController
{

    #[Api]
    #[GetMapping("index/lists")]
    public function list()
    {
        $where = [];
        $keyword = $this->request->input('keyword', '');
        $username = trim($this->request->input('username', ""));
        $user_id = (int)$this->request->input('user_id', 0);
        if ($keyword != '') {
            $where[] = ['description', 'like', '%' . $keyword . '%'];
        }
        if ($user_id > 0) {
            $where[] = ['user_id', '=', $user_id];
        }
        $builder = UserShareReward::where($where)
            ->with(['user'])
            ->orderByDesc('reward_id');
        if ($username != '') {
            $user_ids = User::where('username', 'like', '%' . $username . '%')
                ->pluck('user_id')
                ->toArray() ?: [0];
            $builder->whereIn('user_id', $user_ids);
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
