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
        Schema::table('posts', function (Blueprint $table) {
            $table->dateTime('published_at')->nullable()->after('author_id');
            $table->unsignedBigInteger('feature_media_id')->nullable()->after('legacy_id');
            $table->boolean('commentable')->default(true)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('published_at');
            $table->dropColumn('feature_media_id');
            $table->dropColumn('commentable');
        });
    }
};
