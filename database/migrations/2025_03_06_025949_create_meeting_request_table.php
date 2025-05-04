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
        Schema::create('meeting_request', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("student_id")->nullable();
            $table->unsignedBigInteger("tutor_id")->nullable();
            $table->foreign("tutor_id")->references("id")->on("tutors")->onDelete("cascade");
            $table->foreign("student_id")->references("id")->on("students")->onDelete("cascade");

            $table->string("reason");
            $table->timestampTz('arrange_date');
            $table->boolean("approved");
            $table->string("topic");
            $table->enum("meeting_type",['online',"offline"]);
            $table->string("location")->nullable();
            $table->string("reject_reason")->nullable();
            $table->unsignedBigInteger("approved_arrange_id")->nullable();
            $table->foreign("approved_arrange_id")->references('id')->on('arrangings')->cascadeOnDelete();
            $table->timestampTz("approved_reject_date")->nullable();
            $table->enum("status",["pending","approved","reject","cancelled"])->default("pending");

            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_request');
    }
};
