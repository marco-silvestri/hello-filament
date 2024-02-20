<?php

use App\Enums\Cms\NewsletterStatusEnum;
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
        Schema::create(config('cms.newsletter_table', 'newsletters'), function (Blueprint $table) {
            $statusEnumValues =  config('cms.newsletter_status_enum')::getValues();
            $statusEnumDefault = config('cms.newsletter_default_status_enum');

            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->string('pre_header')->nullable();
            $table->dateTime('send_date')->nullable();
            $table->integer('number')->nullable();
            $table->string('type')->nullable();
            $table->json('json_content')->nullable();
            $table->enum('status', $statusEnumValues)
                ->default($statusEnumDefault);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('cms.newsletter_table', 'newsletters'));
    }
};
