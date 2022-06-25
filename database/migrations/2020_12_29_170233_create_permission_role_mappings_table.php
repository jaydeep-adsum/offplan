<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionRoleMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permission_role_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable();
            $table->string('permissions_id')->nullable();
            $table->string('read')->default(0);
            $table->string('create')->default(0);
            $table->string('update')->default(0);
            $table->string('delete')->default(0);
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
        Schema::dropIfExists('permission_role_mappings');
    }
}
