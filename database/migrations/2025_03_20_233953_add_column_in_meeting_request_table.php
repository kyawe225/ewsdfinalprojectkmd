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
        Schema::table('meeting_request', function (Blueprint $table) {
            $table->addColumn("string","meeting_app")->nullable();
            $table->addColumn("string","online_meeting_applicaiton")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meeting_request', function (Blueprint $table) {
            $table->dropColumn("meeting_app");
        });
    }
};
