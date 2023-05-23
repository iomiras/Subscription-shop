<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->date('planned_delivery_date');
            $table->string('planned_time_slot');
            $table->timestamp('delivery_timestamp')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'delivered'])->default('pending');
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders');
        });
    }

    public function down()
    {
        Schema::dropIfExists('deliveries');
    }
};