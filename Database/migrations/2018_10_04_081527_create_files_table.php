<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('original_name');
            $table->string('given_name');
            $table->nullableMorphs('owner', 'owner_index');
            $table->string('file_type')->nullable();
            $table->string('sub_file_type')->nullable();
            $table->string('mime_type');
            $table->unsignedInteger('size');
            $table->string('folder');
            $table->ipAddress('uploaded_from_ip');
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
        Schema::dropIfExists('files');
    }
}
