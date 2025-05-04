<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            $this->call(RoleAndPermissionSeeder::class);
            $this->call(TutorSeeder::class);
            $this->call(StudentSeeder::class);
            $this->call(StaffSeeder::class);
            $this->call(AllocationSeeder::class);
            $this->call(ArrangingSeeder::class);
            // $this->call(BlogSeeder::class); // ✅ Add BlogSeeder

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Seeding failed: " . $e->getMessage());
        }
    }
}
