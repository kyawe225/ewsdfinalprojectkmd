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
        Schema::create('meeting_record', function (Blueprint $table) {
            $table->id()->primary()->autoIncrement();
            $table->string('meeting_note')->nullable();
            $table->unsignedBigInteger("meeting_detail_id");
            $table->foreign("meeting_detail_id")
                ->references('id')->on('meeting_detail')
                ->onDelete('cascade');

            $table->text("uploaded_document")->nullable();
            $table->softDeletesTz();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_record');
    }
};
