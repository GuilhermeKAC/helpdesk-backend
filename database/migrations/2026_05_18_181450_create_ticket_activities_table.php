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
        Schema::create('ticket_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 50);
            $table->jsonb('old_value')->nullable();
            $table->jsonb('new_value')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['ticket_id', 'created_at'], 'activities_ticket_created_idx');
            $table->index('user_id', 'activities_user_idx');
            $table->index('action', 'activities_action_idx');
            $table->index('created_at', 'activities_created_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_activities');
    }
};
