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
        Schema::create('communication_contact', function (Blueprint $table) {
            $table->unsignedBigInteger('communication_id');
            $table->unsignedBigInteger('contact_id');

            $table->foreign('communication_id')
                ->on('communications')
                ->references('id')
                ->onDelete('cascade');

            $table->foreign('contact_id')
                ->on('contacts')
                ->references('id')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communication_contact');
    }
};
