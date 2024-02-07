<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        $res = Artisan::call('app:clean-slugs');

        if ($res) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('slug');
            });

            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('slug');
            });

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('slug');
            });

            Schema::table('tags', function (Blueprint $table) {
                $table->dropColumn('slug');
            });

        } else {
            abort(403);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('slug');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->string('slug');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('slug');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->string('slug');
        });

    }
};
