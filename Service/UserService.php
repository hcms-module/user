<?php

declare(strict_types=1);

namespace App\Application\User\Service;

use App\Application\User\Event\UserLoginSuccessEvent;
use App\Application\User\Model\User;
use App\Application\User\Model\UserLoginToken;
use App\Exception\ErrorException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class UserService
{

    #[Inject]
    protected EventDispatcherInterface $dispatcher;

    public function logoutByToken(string $token): bool
    {
        try {
            $jwtGuard = auth()->guard('api_auth');

            return $jwtGuard->logout($token);
        } catch (\Throwable $exception) {
            return false;
        }
    }

    public function getUserByToken(string $token): ?User
    {
        try {
            $jwtGuard = auth()->guard('api_auth');
            $user = $jwtGuard->user($token);
            if ($user instanceof User) {
                return $user;
            }

            return null;
        } catch (\Throwable $exception) {
            return null;
        }
    }

    /**
     * 获取当前登录用户
     *
     * @param bool $force
     * @return User|null
     * @throws ErrorException
     */
    public function getLoginInfo(bool $force = true): ?User
    {
        try {
            return (new User())->getLoginUserInfo();
        } catch (\Throwable $exception) {
            if ($force) {
                throw new ErrorException($exception->getMessage(), 501);
            }
        }

        return null;
    }

    /**
     * 通过账号密码注册
     *
     * @param string $username
     * @param string $password
     * @param string $phone
     * @return User
     * @throws ErrorException
     */
    public function register(string $username, string $password, string $phone = ''): User
    {
        $user = User::firstOrNew(['username' => $username]);
        if ($user->user_id > 0) {
            throw new ErrorException('该账号已存在');
        }
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->phone = $phone;
        $user->register_ip = getIp();
        $user->login_ip = getIp();
        if (!$user->save()) {
            throw new ErrorException('创建用户失败');
        }

        return $user;
    }


    /**
     * 通过账号密码登录
     *
     * @param string           $username
     * @param string           $password
     * @param RequestInterface $request
     * @return string
     * @throws ErrorException
     */
    public function loginByPassword(string $username, string $password, RequestInterface $request): string
    {
        $user = User::where(['username' => $username])
            ->first();
        if (!$user instanceof User) {
            throw new ErrorException('账号密码错误');
        }
        if (!password_verify($password, $user->password)) {
            //密码不正确
            throw new ErrorException('账号密码错误');
        }
        $user->login_ip = getIp();
        if ($user->save()) {
            $token = (string)$user->login();
            $this->createUseLoginToken($user->user_id, $token, $request);

            //创建登录记录
            return $token;
        }

        throw new ErrorException('登录失败');
    }

    /**
     * 登录成功创建token
     *
     * @param int              $user_id
     * @param string           $token
     * @param RequestInterface $request
     * @return bool
     */
    public function createUseLoginToken(int $user_id, string $token, RequestInterface $request): bool
    {
        $user_login_token = UserLoginToken::create([
            'user_id' => $user_id,
            'token' => $token,
            'user_agent' => $request->getHeaderLine('User-Agent'),
            'remote_ip' => getIp(),
        ]);
        if ($user_login_token instanceof UserLoginToken) {
            //创建成功，触发事件
            $this->dispatcher->dispatch(new UserLoginSuccessEvent($user_login_token));

            return true;
        }

        return false;
    }
}
