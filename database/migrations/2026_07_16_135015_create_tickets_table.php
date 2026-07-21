<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enterprise_id')->constrained('enterprises');
            $table->foreignId('sector_id')->constrained('sectors');
            $table->foreignId('requester_id')->constrained('users');
            $table->foreignId('attendant_id')->nullable()->constrained('users');
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['open', 'in_progress', 'concluded', 'cancelled', 'reopened'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high'])->default('low');
            $table->boolean('has_attachments')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
