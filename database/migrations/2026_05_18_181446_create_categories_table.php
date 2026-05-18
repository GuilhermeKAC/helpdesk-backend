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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->char('color', 7)->default('#3B82F6');
            $table->string('icon')->default('DocumentIcon');
            $table->foreignId('auto_assign_technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('sla_hours')->default(48);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active', 'categories_active_idx');
            $table->index('name', 'categories_name_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
