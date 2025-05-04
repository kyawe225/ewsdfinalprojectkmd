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
        Schema::create('allocations', function (Blueprint $table) {
            $table->id();
            $table->string("allocation_date");
            $table->string("allocated_by");
            $table->unsignedBigInteger("staff_id");
            $table->unsignedBigInteger("tutor_id");
            $table->unsignedBigInteger("student_id");
            // if we are using foreign key or from mis?
            $table->foreign("tutor_id")->on("tutors")->references("id")->onDelete("cascade");
            $table->foreign("student_id")->on("students")->references("id")->onDelete("cascade");
            $table->foreign("staff_id")->on("staffs")->references("id")->onDelete("cascade");
            // endregion
            $table->softDeletes();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allocations');
    }
};
