<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateUserShareRewardTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_share_reward', function (Blueprint $table) {
            $table->bigIncrements('reward_id');
            $table->bigInteger('user_id');
            $table->string('description', 512)
                ->nullable(false)
                ->default('')
                ->comment('记录描述');
            $table->string('target', 32)
                ->nullable(false)
                ->default('')
                ->comment('来源标识');
            $table->string('target_type', 32)
                ->nullable(false)
                ->default('')
                ->comment('标识类型');
            $table->bigInteger('reward_amount')
                ->nullable(false)
                ->default(0)
                ->comment('金额');
            $table->bigInteger('balance')
                ->nullable(false)
                ->default(0)
                ->comment('余额');
            $table->tinyInteger('status')
                ->nullable(false)
                ->default(1)
                ->comment('记录状态，1正常、0是冻结');
            $table->datetimes();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_share_reward');
    }
}
