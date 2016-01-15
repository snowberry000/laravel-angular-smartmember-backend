<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTemplatesDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages_templates_data', function (Blueprint $table) {
            $table->increments('id');
            $table->string("title");
            $table->string("key");
            $table->string("value");
            $table->integer("position");
            $table->integer("element_type_id");
            $table->bigInteger("page_id", false, true);
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
        Schema::drop('pages_templates_data');
    }
}
