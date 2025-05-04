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

        //-------Don't eliminate author_role column, it is important. Please----//
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string("author");
            $table->string("title");
            $table->text("content");
            $table->string('author_role');
            $table->unsignedBigInteger("student_id")->nullable();
            $table->unsignedBigInteger("tutor_id")->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->foreign("tutor_id")->references("id")->on("tutors")->onDelete("cascade");
            $table->foreign("student_id")->references("id")->on("students")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
