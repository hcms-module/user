<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateUserVipTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_vip', function (Blueprint $table) {
            $table->bigIncrements('user_vip_id');
            $table->bigInteger('user_id')
                ->default(0)
                ->nullable(false)
                ->comment('所属用户');
            $table->tinyInteger('vip_type')
                ->default(0)
                ->nullable(false)
                ->comment('Vip类型，根据业务需要定义VIP类型');
            $table->integer('expire_time')
                ->default(0)
                ->nullable(false)
                ->comment('到期时间 [时间戳]');
            $table->tinyInteger('status')
                ->default(1)
                ->nullable(false)
                ->comment('Vip状态：1正常，0未激活，2禁用、3失效 …');
            $table->datetimes();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_vip');
    }
}

;
