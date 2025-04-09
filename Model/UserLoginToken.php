<?php

declare(strict_types=1);

namespace App\Application\User\Model;

use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $id
 * @property int            $user_id
 * @property string         $token
 * @property string         $user_agent
 * @property string         $remote_ip
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
 */
class UserLoginToken extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'user_login_token';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'user_id',
        'token',
        'user_agent',
        'remote_ip',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
