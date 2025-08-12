<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_pincodes', function (Blueprint $table) {
            $table->id();
            $table->string('city', 100);
            $table->string('pincode', 6);
            $table->timestamps();
            $table->unique(['city','pincode']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('city_pincodes');
    }
};
