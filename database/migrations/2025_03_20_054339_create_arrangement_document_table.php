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
        Schema::dropIfExists("arrangement_document");
        Schema::create('arrangement_document', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("assignment_arrangement_id");
            $table->foreign("assignment_arrangement_id")
                ->references('id')->on('assignment_arrangements')
                ->onDelete('cascade');
            $table->unsignedBigInteger("document_id");
            $table->foreign("document_id")->references("id")->on("document")->onDelete('cascade');
            
            $table->unsignedBigInteger('created_by');
            $table->enum("created_type",['tutor','student']);
            $table->enum('arrangement_type',['meeting','assignment'])->default('meeting');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arrangement_document');
    }
};
