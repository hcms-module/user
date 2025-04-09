<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateUserShareWithdrawTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_share_withdraw', function (Blueprint $table) {
            $table->bigIncrements('withdraw_id');
            $table->bigInteger('user_id')
                ->nullable(false)
                ->default(0)
                ->comment('用户ID');
            $table->bigInteger('withdraw_amount')
                ->nullable(false)
                ->default(0)
                ->comment('提现金额');
            $table->tinyInteger('withdraw_type')
                ->nullable(false)
                ->default(0)
                ->comment('');
            $table->string('real_name', 128)
                ->nullable(false)
                ->nullable('')
                ->comment('真实姓名');
            $table->string('account', 128)
                ->nullable(false)
                ->default('')
                ->comment('收款账号');
            $table->tinyInteger('status')
                ->default(0)
                ->nullable(false)
                ->comment('0已提交、1已通过、2已打款、3取消、4 拒绝');
            $table->string('reject_msg', 255)
                ->nullable(false)
                ->default('')
                ->comment('拒绝理由');
            $table->datetimes();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_share_withdraw');
    }
}
