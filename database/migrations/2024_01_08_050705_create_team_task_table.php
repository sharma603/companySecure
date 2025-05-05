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
        Schema::create('team_task', function (Blueprint $table) {
            $table->id();
            $table->string('assigned_to', 10)->nullable();
            $table->string('task_desc', 100)->nullable();
            $table->date('assigned_date');
            $table->date('due_date');
            $table->string('priority', 10)->nullable();
            $table->string('status', 30)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_task');
    }
};
