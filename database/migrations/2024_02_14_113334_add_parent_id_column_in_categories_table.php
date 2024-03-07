<?php

use App\Models\Category;
use App\Traits\Cms\HasWpData;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use HasWpData;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->after('id')->nullable();

            $table->foreign('parent_id')
                ->references('id')
                ->on('categories')
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign('categories_parent_id_foreign');
            $table->dropColumn('parent_id');
        });
    }
};
