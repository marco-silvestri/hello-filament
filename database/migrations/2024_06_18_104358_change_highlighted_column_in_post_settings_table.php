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
            $table->boolean('highlighted')->nullable()->after('accessible_for')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_settings', function (Blueprint $table) {
            $table->date('highlighted')->after('accessible_for')->change();
        });
    }
};
