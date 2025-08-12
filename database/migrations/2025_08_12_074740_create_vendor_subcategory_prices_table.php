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
       Schema::create('vendor_subcategory_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('subcategory_id');
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['vendor_id', 'subcategory_id']);

            // Adjust FKs to your real tables:
            // If vendors are users:
            $table->foreign('vendor_id')->references('id')->on('users')->onDelete('cascade');
            // Or if you have a vendors table, swap 'users' with 'vendors'.

            $table->foreign('subcategory_id')->references('id')->on('sub_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_subcategory_prices');
    }
};
