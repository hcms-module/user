<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateUserVipRecordTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_vip_record', function (Blueprint $table) {
            $table->bigIncrements('record_id');
            $table->bigInteger('user_id')
                ->default(0)
                ->nullable(false)
                ->comment('所属用户');
            $table->bigInteger('user_vip_id')
                ->default(0)
                ->nullable(false)
                ->comment('所属Vip记录');
            $table->integer('origin_expire_time')
                ->default(0)
                ->nullable(false)
                ->comment('原来过期时间');
            $table->integer('new_expire_time')
                ->default(0)
                ->nullable(false)
                ->comment('新过期时间');
            $table->integer('change_time')
                ->default(0)
                ->nullable(false)
                ->comment('新旧续费时间之差，已过期的按照当前时间计算，未过期按照过期时间计算');
            $table->bigInteger('target')
                ->default(0)
                ->nullable(false)
                ->comment('续费来源表示，例如订单id');
            $table->string('target_type', 32)
                ->default('')
                ->nullable(false)
                ->comment('续费来源标识类型，例如order代表续费订单');
            $table->datetimes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_vip_record');
    }
}

;
