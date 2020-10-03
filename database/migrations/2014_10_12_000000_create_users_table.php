<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->mediumText('image');
            $table->string('mobile_number');
            $table->string('fathers_name');
            $table->string('mothers_name');
            $table->date('date_of_birth');
            $table->string('address');
            $table->string('thana');
            $table->string('token');
            $table->string('NID_or_birth_certificate_number');
            $table->mediumText('nid_image');
            $table->string('nominee_name')->nullable();
            $table->string('nominee_nid')->nullable();
            $table->string('refer_account_number')->nullable();
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
        Schema::dropIfExists('users');
    }
}
