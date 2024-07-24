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
        Schema::table('post_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('sponsor_id')->nullable()->after('accessible_for');
            $table->dropColumn('highlighted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_settings', function (Blueprint $table) {
            $table->dropColumn('sponsor_id');
            $table->boolean('highlighted')->nullable();
        });
    }
};
