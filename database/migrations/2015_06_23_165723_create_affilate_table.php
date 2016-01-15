<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffilateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliates', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger("page_id", false, true);
            $table->integer("affiliate_type_id", false, true);
            $table->bigInteger("affiliate_request_id", false, true);
            $table->integer("user_id", false, true);
            $table->string("user_name");
            $table->string("user_email");
            $table->string("user_country");
            $table->text("user_note");
            $table->text("admin_note");
            $table->integer("past_sales");
            $table->string("product_name");
            $table->string("original");
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
        Schema::drop('affiliates');
    }
}
