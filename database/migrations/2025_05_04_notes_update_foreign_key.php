<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            // Drop the foreign key constraint to users table
            $table->dropForeign(['user_id']);

            // Add new foreign key to registers table
            $table->foreign('user_id')
                ->references('id')
                ->on('registers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            // Drop the foreign key constraint to registers table
            $table->dropForeign(['user_id']);

            // Add back the original foreign key to users table
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }
}; 