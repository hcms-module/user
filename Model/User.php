<?php

declare(strict_types=1);

namespace App\Application\User\Model;

use App\Exception\ErrorException;
use App\Model\AbstractAuthModel;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\SoftDeletes;

/**
 * @property int            $user_id
 * @property string         $username
 * @property string         $password
 * @property string         $phone
 * @property string         $register_ip
 * @property string         $login_ip
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
 */
class User extends AbstractAuthModel
{
    use SoftDeletes;

    protected string $primaryKey = "user_id";
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'user';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['username'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['user_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    protected array $hidden = ['password'];

    public function getLoginUserInfo(): self
    {
        $user = $this->getLoginUser();
        if ($user instanceof self) {
            return $user;
        }
        throw new ErrorException('登录失败');
    }

    public function vips(): HasMany
    {
        return $this->hasMany(UserVip::class, 'user_id', 'user_id');
    }
}
