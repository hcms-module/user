<?php

declare(strict_types=1);

namespace App\Application\User\Controller;


use App\Annotation\Api;
use App\Annotation\View;
use App\Application\Admin\Middleware\AdminMiddleware;
use App\Application\User\Model\UserShareReward;
use App\Controller\AbstractController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;

#[Middleware(AdminMiddleware::class)]
#[Controller("user/sharereward")]
class ShareRewardController extends AbstractController
{

    /**
     * @Api()
     * @GetMapping("index/lists")
     */
    #[Api]
    #[GetMapping("index/lists")]
    public function list()
    {
        $where = [];
        $keyword = $this->request->input('keyword', '');
        $user_id = (int)$this->request->input('user_id', 0);
        if ($user_id > 0) {
            $where[] = ['user_id', $user_id];
        }
        if ($keyword > 0) {
            $where[] = ['description', 'like', '%' . $keyword . '%'];
        }
        $lists = UserShareReward::where($where)
            ->orderByDesc('reward_id')
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
