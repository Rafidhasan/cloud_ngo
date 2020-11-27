<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('amount');
            $table->integer('installments');
            $table->integer('perInstallmentAmount');
            $table->string('business_name');
            $table->string('business_Address');
            $table->string('business_type');
            $table->string('contact_business');
            $table->string('exp_business');
            $table->string('capital');
            $table->string('fee')->nullable();
            $table->boolean('approved')->default(0);
            $table->boolean('completed')->default(0);
            $table->boolean('paid')->default(0);
            $table->dateTime('approved_date')->nullable();
            $table->boolean('g_approved')->default(0);
            $table->string('token');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::create('employee_loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('org_name');
            $table->string('exp');
            $table->string('office_no');
            $table->string('position');
            $table->integer('salary');
            $table->string('fee')->nullable();
            $table->boolean('approved')->default(0);
            $table->boolean('completed')->default(0);
            $table->boolean('paid')->default(0);
            $table->dateTime('approved_date')->nullable();
            $table->boolean('g_approved')->default(0);
            $table->integer('amount');
            $table->integer('installments');
            $table->integer('perInstallmentAmount');
            $table->string('token');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::create('edu_loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('org_name');
            $table->string('org_address');
            $table->string('level');
            $table->string('edu_no');
            $table->integer('amount');
            $table->integer('installments');
            $table->integer('perInstallmentAmount');
            $table->string('token');
            $table->string('fee')->nullable();
            $table->boolean('approved')->default(0);
            $table->boolean('completed')->default(0);
            $table->boolean('paid')->default(0);
            $table->dateTime('approved_date')->nullable();
            $table->boolean('g_approved')->default(0);
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::create('garantors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_id')->nullable();
            $table->string('loan_method')->nullable();
            $table->string('g_name')->nullable();
            $table->string('g_mobile_number')->nullable();
            $table->boolean('g_approved')->default(0);
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
        Schema::dropIfExists('loans');
    }
}
