<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('owner');
            $table->string('className');
            $table->string('fileTypes');
            $table->string('subFileTypes')->nullable();
            $table->integer('maxFileCounts')->nullable();
            $table->boolean('hasCrop')->default(false);
            $table->boolean('hasResize')->default(false);
            $table->string('mimes')->nullable();
            $table->boolean('isRequired')->default(false);
            $table->unique(['owner', 'className', 'fileTypes', 'subFileTypes']);
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
        Schema::dropIfExists('file_types');
    }
}
