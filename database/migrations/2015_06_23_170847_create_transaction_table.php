<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger("page_id", false, true);
            $table->bigInteger("user_id", false, true);
            $table->integer("transaction_type_id", false, true);
            $table->string("source");
            $table->bigInteger("affiliate_id", false, true);
            $table->bigInteger("product_id", false, true);
            $table->string("name");
            $table->string("email");
            $table->string("payment_method");
            $table->float("price");
            $table->string("association_hash");
            $table->text("data");
            $table->softDeletes();
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
        Schema::drop('transactions');
    }
}
