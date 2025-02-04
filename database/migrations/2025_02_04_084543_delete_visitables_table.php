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
        Schema::dropIfExists('visitables');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('visitables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('visit_id');
            $table->string('visitable_type');
            $table->unsignedBigInteger('visitable_id');

            $table->foreign('visit_id')
                ->on('visits')
                ->references('id')
                ->onDelete('cascade');
        });
    }
};
