<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateUserLoginTokenTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_login_token', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')
                ->nullable(false)
                ->default(0)
                ->comment('所属用户id');
            $table->string('token', 512)
                ->default('')
                ->nullable(false)
                ->comment('登录凭证');
            $table->string('user_agent', 255)
                ->default('')
                ->nullable(false)
                ->comment('浏览器代理');
            $table->string('remote_ip', 128)
                ->nullable(false)
                ->default('')
                ->comment('登录IP地址');
            $table->datetimes();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_login_token');
    }
}

;
