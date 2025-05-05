<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('labels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color');
            $table->timestamps();
        });

        Schema::create('note_label', function (Blueprint $table) {
            $table->foreignId('note_id')->constrained()->onDelete('cascade');
            $table->foreignId('label_id')->constrained()->onDelete('cascade');
            $table->primary(['note_id', 'label_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('note_label');
        Schema::dropIfExists('labels');
    }
}; 