<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->increments('id');
			$table->string('user', 100)->comment('用户名');
			$table->string('email', 100)->comment('邮箱');
			$table->enum('sex', ['f', 'm'])->comment('性别');
			$table->string('hoby', 100)->comment('爱好');
			$table->tinyInteger('status')->comment('状态');
            $table->timestamps();
			$table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tests');
    }
}
