<?php

declare(strict_types=1);

namespace App\Application\User\Model;

use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $user_vip_id
 * @property int            $user_id
 * @property int            $vip_type
 * @property int            $expire_time
 * @property int            $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
 * @property-read string    $vip_type_name
 * @property-read string    $expire_time_string
 */
class UserVip extends Model
{
    use SoftDeletes;

    protected string $primaryKey = 'user_vip_id';
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'user_vip';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['user_id', 'vip_type'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'user_vip_id' => 'integer',
        'user_id' => 'integer',
        'vip_type' => 'integer',
        'expire_time' => 'integer',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    const STATUS_INACTIVE = 0;
    const STATUS_VALID = 1;
    const STATUS_FORBIDDEN = 2;
    const STATUS_INVALID = 3;
    const VIP_TYPES = [];

    //默认会员类型
    const VIP_TYPE_DEFAULT = 0;

    protected array $appends = ['vip_type_name', 'expire_time_string'];

    protected function getVipTypeNameAttribute(): string
    {
        return self::VIP_TYPES[$this->vip_type] ?? '其他';
    }

    protected function getExpireTimeStringAttribute(): string
    {
        return date('y-m-d H:i:s', $this->attributes['expire_time']);
    }
}
