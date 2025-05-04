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
        Schema::create('arrangings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("student_id");
            $table->unsignedBigInteger("tutor_id");
            $table->foreign("tutor_id")->references("id")->on("tutors")->onDelete('cascade');
            $table->foreign("student_id")->references("id")->on("students")->onDelete('cascade');
            $table->enum("status",["pending","completed","canceled"]);
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arrangings');
    }
};
