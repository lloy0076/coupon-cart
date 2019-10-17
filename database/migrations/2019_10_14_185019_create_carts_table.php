<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_id', 40)->unique();
            $table->bigInteger('user_id')->unsigned();
            $table->dateTime('submitted_date')->nullable();
            $table->text('instructions')->nullable();
            $table->bigInteger('coupon_id')->unsigned()->nullable();
            $table->decimal('total_inc', 8, 2)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carts', function(Blueprint $table)
        {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['user_id']);
            }
        });
        Schema::dropIfExists('carts');
    }
}
