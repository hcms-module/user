<?php

declare (strict_types=1);

namespace App\Application\User\Model;

use Hyperf\Database\Model\Model;
use Hyperf\Database\Model\SoftDeletes;

/**
 * @property int            $withdraw_id
 * @property string         $withdraw_amount
 * @property int            $user_id
 * @property int            $withdraw_type
 * @property string         $real_name
 * @property int            $status
 * @property string         $reject_msg
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
 */
class ShareWithdraw extends Model
{
    use SoftDeletes;

    protected string $primaryKey = 'withdraw_id';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected ?string $table = 'user_share_withdraw';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = [
        'withdraw_amount',
        'user_id',
        'withdraw_type',
        'real_name',
        'account',
        'status'
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $casts = [
        'withdraw_id' => 'integer',
        'user_id' => 'integer',
        'withdraw_type' => 'integer',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    const STATUS_PENDING = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_REMIT = 2; //已打款
    const STATUS_CANCEL = 3; //已取消
    const STATUS_REJECT = 4; //已拒绝


    const TYPE_ALIPAY = 0;
    const TYPE_WXPAY = 1;

    protected array $appends = [
        'status_text'
    ];

    function getStatusTextAttribute(): string
    {
        $status = $this->attributes['status'] ?? self::STATUS_PENDING;
        switch ($status) {
            case self::STATUS_PENDING:
                return "审核中";
            case self::STATUS_SUCCESS:
                return "待打款";
            case self::STATUS_REMIT:
                return "已打款";
            case self::STATUS_CANCEL:
                return "已取消";
            case self::STATUS_REJECT:
                return "已拒绝";
            default:
                return "其他";
        }
    }
}