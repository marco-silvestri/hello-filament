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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('legacy_id')->after('id');
            $table->text('description')->after('email')->nullable();
            $table->text('slug')->after('name');
            $table->text('url')->before('avatar')->nullable();
            $table->text('avatar')->before('created_at')->nullable();
            //$table->text('display_name')->after('slug')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //$table->dropColumn(['legacy_id','description','slug','url','avatar','display_name']);
            $table->dropColumn(['legacy_id','description','slug','url','avatar']);
        });
    }
};
