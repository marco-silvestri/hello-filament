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
        Schema::table('pages', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            Schema::table('pages', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });

            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });

            Schema::table('tags', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        });
    }
};
