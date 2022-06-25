<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitMultipleAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unit_multiple_attachments', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default('0');
            $table->integer('project_id')->default('0');
            $table->string('attachment_name')->nullable();
            $table->text('attachment_multiple')->nullable();
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
        Schema::dropIfExists('unit_multiple_attachments');
    }
}
