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
        Schema::create('time_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->nullable(false);
            $table->foreignId('client_id')->constrained()->onDelete('cascade')->nullable(false);
            $table->date('start_date')->nullable(false);
            $table->foreignId('project_id')->constrained()->onDelete('cascade')->nullable(false);
            $table->string('task')->nullable(false);
            $table->boolean('planned')->nullable(false);
            $table->string('task_summary');
            $table->integer('duration')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_entries');
    }
};
