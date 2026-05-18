<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 20)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('status', 20)->default('open');
            $table->string('priority', 10)->default('medium');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->integer('response_time')->nullable();
            $table->integer('resolution_time')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'status'], 'tickets_user_status_idx');
            $table->index(['technician_id', 'status'], 'tickets_tech_status_idx');
            $table->index(['status', 'priority'], 'tickets_status_priority_idx');
            $table->index('ticket_number', 'tickets_number_idx');
            $table->index('created_at', 'tickets_created_at_idx');
        });

        DB::unprepared("
            CREATE SEQUENCE IF NOT EXISTS ticket_number_seq;

            CREATE OR REPLACE FUNCTION generate_ticket_number()
            RETURNS TRIGGER AS \$\$
            DECLARE
                year_part TEXT;
                seq_part  TEXT;
            BEGIN
                year_part := to_char(NOW(), 'YYYY');
                seq_part  := LPAD(nextval('ticket_number_seq')::TEXT, 6, '0');
                NEW.ticket_number := 'HD-' || year_part || '-' || seq_part;
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;

            CREATE TRIGGER set_ticket_number
                BEFORE INSERT ON tickets
                FOR EACH ROW
                EXECUTE FUNCTION generate_ticket_number();
        ");

        DB::statement('CREATE INDEX tickets_metadata_gin_idx ON tickets USING GIN (metadata)');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS set_ticket_number ON tickets');
        DB::unprepared('DROP FUNCTION IF EXISTS generate_ticket_number');
        DB::unprepared('DROP SEQUENCE IF EXISTS ticket_number_seq');
        Schema::dropIfExists('tickets');
    }
};
