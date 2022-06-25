<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Developer;
use App\Models\MultipleContacts;

class MultipleContact extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('multiplecontact', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('developer_id')->default('0');
            $table->text('person')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        foreach(Developer::all() as $item)
        {
            MultipleContacts::create(['person' => $item -> person,'phone' => $item -> phone, 'developer_id' => $item -> id]);
        }

        // Schema::table('developer', function (Blueprint $table) {
        //     $table->dropColumn('person');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
