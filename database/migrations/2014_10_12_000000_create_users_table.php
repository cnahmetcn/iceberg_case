<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string("fullName", 50);
            $table->string("email", 50)->unique();
            $table->string("phone", 15)->nullable();
            $table->string("password", 64);
            $table->string("access_token", 1000)->nullable();
            $table->tinyInteger("hold")->default(0)
                ->comment = 'activate 0 / deactivate 1 ';
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
