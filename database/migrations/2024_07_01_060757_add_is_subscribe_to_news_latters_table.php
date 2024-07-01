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
        Schema::table('news_letters', function (Blueprint $table) {
            $table->boolean('is_subscribe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news_latters', function (Blueprint $table) {
            $table->dropColumn('is_subscribe');
        });
    }
};
