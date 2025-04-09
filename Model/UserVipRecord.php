<?php

declare(strict_types=1);

namespace App\Application\User\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $record_id
 * @property int            $user_id
 * @property int            $user_vip_id
 * @property int            $origin_expire_time
 * @property int            $new_expire_time
 * @property int            $change_time
 * @property int            $target
 * @property string         $target_type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class UserVipRecord extends Model
{

    protected string $primaryKey = 'record_id';
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'user_vip_record';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'user_id',
        'user_vip_id',
        'new_expire_time',
        'origin_expire_time',
        'change_time',
        'target',
        'target_type'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'record_id' => 'integer',
        'user_id' => 'integer',
        'user_vip_id' => 'integer',
        'origin_expire_time' => 'integer',
        'new_expire_time' => 'integer',
        'change_time' => 'integer',
        'target' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
