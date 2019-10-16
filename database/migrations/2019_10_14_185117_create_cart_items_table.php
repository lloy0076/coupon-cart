<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_items',
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('cart_id')->unsigned();
                $table->bigInteger('product_id')->unsigned();
                $table->integer('quantity')->unsigned();
                $table->decimal('price_inc', 8, 2);
                $table->timestamps();

                $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cart_items', function(Blueprint $table)
        {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['cart_id']);
            }
        });

        Schema::dropIfExists('cart_items');
    }
}
