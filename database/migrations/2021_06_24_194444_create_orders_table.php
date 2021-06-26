<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('payment_method');
            $table->string('shipping_method');
            $table->foreignId('customer_id')->nullable()->constrained();
            $table->integer('company_id');
            $table->string('type');
            $table->foreignId('billing_address_id')->nullable()->constrained('addresses');
            $table->foreignId('shipping_address_id')->nullable()->constrained('addresses');
            $table->string('total');
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
        Schema::dropIfExists('orders');
    }
}
