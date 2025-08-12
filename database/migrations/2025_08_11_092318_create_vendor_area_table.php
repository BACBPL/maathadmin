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
      Schema::create('vendor_area', function (Blueprint $table) {
    $table->id();
    $table->foreignId('v_id')
          ->constrained('vendor_details') // vendor_details.id
          ->cascadeOnDelete();
    $table->text('service_area')->nullable(); // "700001-700002-700003"
    $table->boolean('verified')->default(false); // added default
    $table->timestamps();
    $table->unique('v_id'); // one row per vendor
});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_area');
    }
};
