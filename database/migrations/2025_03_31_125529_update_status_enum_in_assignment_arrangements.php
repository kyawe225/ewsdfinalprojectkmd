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
        // Create a new enum type with all values
        DB::statement("
            CREATE TYPE assignment_status_new AS ENUM(
                'pending', 
                'accepted', 
                'in_progress', 
                'submitted', 
                'reviewed', 
                'finished', 
                'canceled', 
                'overdue'
            )
        ");
        
        // Update the column type to the new enum
        DB::statement("
            ALTER TABLE assignment_arrangements 
            ALTER COLUMN status TYPE assignment_status_new 
            USING status::text::assignment_status_new
        ");
        
        // Drop the old enum type
        DB::statement("DROP TYPE IF EXISTS assignment_status_old");
        
        // Rename the new enum type to the standard name (optional)
        DB::statement("
            ALTER TYPE assignment_status_new 
            RENAME TO assignment_status
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignment_arrangements', function (Blueprint $table) {
            //
        });
    }
};
