<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplatesAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('templates_attributes', function (Blueprint $table) {
            $table->increments('id');
            $table->string("default_title");
            $table->string("key");
            $table->string("default_value");
            $table->integer("default_position");
            $table->integer("element_type_id");
            $table->bigInteger("template_id", false, true);
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
        Schema::drop('templates_attributes');
    }
}
