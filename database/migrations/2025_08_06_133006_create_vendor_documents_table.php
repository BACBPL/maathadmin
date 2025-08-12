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
        Schema::create('vendor_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_detail_id')
                  ->constrained('vendor_details')
                  ->cascadeOnDelete();
            $table->string('aadhar_number');
            $table->string('aadhar_image');
            $table->string('pan_number');
            $table->string('pan_image');
            $table->string('trade_license_number');
            $table->string('trade_license_image');
            $table->string('gst_number');
            $table->string('gst_image');
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
        Schema::dropIfExists('vendor_documents');
    }
};
