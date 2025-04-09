<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user', function (Blueprint $table) {
            $table->bigIncrements('user_id');
            $table->string('username', 128)
                ->default("")
                ->nullable(false)
                ->comment('用户名');
            $table->string('password', 128)
                ->default("")
                ->nullable(false)
                ->comment('密码，建议使用 password_hash 加密');
            $table->string('phone', 128)
                ->default("")
                ->nullable(false)
                ->comment("手机号码");
            $table->string('register_ip', 128)
                ->nullable(false)
                ->default("")
                ->comment('注册ip');
            $table->string('login_ip', 128)
                ->nullable(false)
                ->default("")
                ->comment('登录IP');
            $table->datetimes();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
}
