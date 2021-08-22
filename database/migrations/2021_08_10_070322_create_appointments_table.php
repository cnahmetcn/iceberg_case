<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->bigIncrements('appointmentID');
            $table->integer('userID')->nullable();
            $table->string("name",50);
            $table->string("surname",50);
            $table->string("phone",15);
            $table->string("email",50);

            $table->date("date");
            $table->string("time");

            $table->string("address",10);
            $table->string("longAddress",100);
            $table->string("distance",20)->nullable();
            $table->string("checkoutTime")->nullable();
            $table->string("returnTime")->nullable();

            $table->tinyInteger("tempFreezing")->default(0)
                ->comment = 'activate 0 / deactivate 1 ';

            $table->tinyInteger("done")->default(0)
                ->comment = 'not done 0 / done 1 ';

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
        Schema::dropIfExists('appointments');
    }
}
