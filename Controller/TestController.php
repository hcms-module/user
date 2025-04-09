<?php

declare(strict_types=1);

namespace App\Application\User\Controller;

use App\Application\User\Event\UserLoginSuccessEvent;
use App\Application\User\Model\UserLoginToken;
use App\Annotation\Api;
use App\Application\Shop\Model\ShopOrder;
use App\Application\User\Event\UserRenewalEvent;
use App\Controller\AbstractController;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\EventDispatcher\EventDispatcherInterface;


#[Controller("/user/test")]
class TestController extends AbstractController
{

    #[Inject]
    protected EventDispatcherInterface $dispatcher;

    #[Api]
    #[GetMapping("login")]
    public function index()
    {

        $user_login_token = UserLoginToken::where('user_id',2)
            ->first();

        if ($user_login_token instanceof UserLoginToken) {
            //创建成功，触发事件
            $this->dispatcher->dispatch(new UserLoginSuccessEvent($user_login_token));

            return true;
        }

    }

    #[Api]
    #[PostMapping("shopOrderSettle")]
    public function shopOrderSettle()
    {
        $order_id = $this->request->input('order_id', 0);

        $order = ShopOrder::find($order_id);

        $this->eventDispatcher->dispatch(new UserRenewalEvent($order));

//        $share_reward_service = new ShareRewardService();
//        $share_reward_service->createShareOrderReward($order->order_no);

        return ['title' => '操作成功'];


    }
}
