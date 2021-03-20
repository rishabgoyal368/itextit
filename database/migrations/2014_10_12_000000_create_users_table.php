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
            $table->string('full_name');
            $table->string('email');
            $table->string('mobile_number')->nullable();
            $table->string('country_code')->nullable();
            $table->string('login_type')->nullable();
            $table->string('otp')->nullable();
            $table->string('password');
            $table->string('refrence_id')->nullable();            
            $table->string('calender_id')->nullable();            
            $table->string('profile_image')->nullable();
            $table->string('device_token')->nullable();
            $table->string('status')->default('inactive');
            $table->softDeletes('deleted_at')->nullable();
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
