<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('contact_id');
            $table->string('user_name', 200);
            $table->text('address');
            $table->string('mobile',20);
            $table->string('latitude',50);
            $table->string('longitude',50);
            $table->string('status',20)->default('pending');
            $table->timestamps();
            $table->foreign('contact_id')->references('id')->on('contacts')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_orders', function (Blueprint $table) {
            $table->dropForeign('user_orders_contact_id_foreign');
        });
        Schema::dropIfExists('user_orders');
    }
}
