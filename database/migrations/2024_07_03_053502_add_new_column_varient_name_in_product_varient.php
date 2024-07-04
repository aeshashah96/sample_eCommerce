<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_varients', function (Blueprint $table) {
            //
            $table->string('variant_name')->after('product_color_id');
            $table->string('stock')->change();
            $table->unsignedBigInteger('product_size_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_varients', function (Blueprint $table) {
            //
        });
    }
};
