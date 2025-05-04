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
        Schema::create('assignment_arrangements', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->text('instructions')->nullable();
            $table->text("content")->nullable();
            $table->unsignedBigInteger("arrange_id");
            $table->foreign("arrange_id")
                ->references('id')->on('arrangings')
                ->onDelete('cascade');
            $table->string("feedback");
            $table->timestampTz('dead_line');
            $table->enum("status",[ 'pending', 
            'accepted', 
            'in_progress', 
            'submitted', 
            'reviewed', 
            'finished', 
            'canceled', 
            'overdue']);
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_arrangement');
    }
};
