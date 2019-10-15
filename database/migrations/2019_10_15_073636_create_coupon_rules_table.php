<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('coupon_id')->unsigned();
            $table->enum('rule_type', ['coupon', 'discount', 'other'])->default('coupon');
            $table->string('rule', 255);
            $table->text('description')->nullable();
            $table->integer('rule_order')->nullable();
            $table->boolean('rule_not')->default(false)->nullable();
            $table->string('rule_operator', 255)->default('and');
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
        Schema::dropIfExists('coupon_rules');
    }
}
