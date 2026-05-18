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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->morphs('attachable');
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('filename');
            $table->string('original_name');
            $table->string('mime_type', 100);
            $table->integer('size');
            $table->string('path', 500);
            $table->string('disk', 20)->default('public');
            $table->jsonb('metadata')->nullable();
            $table->timestamps();

            $table->index('user_id', 'attachments_user_idx');
            $table->index('created_at', 'attachments_created_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
