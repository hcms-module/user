<?php

declare (strict_types=1);

namespace App\Application\User\Model;

use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $reward_id
 * @property int            $user_id
 * @property string         $description
 * @property string         $target
 * @property string         $target_type
 * @property int            $reward_amount
 * @property int            $balance
 * @property int            $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
 */
class UserShareReward extends Model
{
    use SoftDeletes;

    protected string $primaryKey = 'reward_id';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected ?string $table = 'user_share_reward';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = [
        'balance',
        'user_id',
        'target',
        'target_type',
        'reward_amount',
        'description'
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $casts = [
        'reward_id' => 'integer',
        'user_id' => 'integer',
        'reward_amount' => 'integer',
        'balance' => 'integer',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}