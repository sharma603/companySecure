<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Drop the existing foreign key
        Schema::table('reminders', function (Blueprint $table) {
            // Try to drop foreign key using a common naming convention
            try {
                $table->dropForeign('reminders_user_id_foreign');
            } catch (\Exception $e) {
                // Ignore if constraint doesn't exist
            }
        });
        
        // Now recreate the constraint with cascade delete
        Schema::table('reminders', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        // We don't really need to revert this change
    }
}; 