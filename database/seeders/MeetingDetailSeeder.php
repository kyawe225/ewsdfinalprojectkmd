<?php

namespace Database\Seeders;

use App\Models\MeetingDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MeetingDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MeetingDetail::create([
            'arrange_date' => '2025-03-25 14:30:00+00', // Timezone-aware timestamp
            'meeting_type' => 'online', // Either 'online' or 'offline'
            'location' => null, // Location for offline meetings, null for online
            'meeting_link' => 'https://zoom.us/j/123456789', // Link for online meetings, null for offline
            'online_meeting_application_type' => 'Zoom', // Application used for online meeting
            'arrange_id' => 2, // Foreign key to arrangings table
            'topic' => 'Project Kickoff Meeting',
            'description' => 'Initial discussion about project scope and timeline', // Optional description
            'status' => 'pending', // One of: "cancelled", "pending", "completed", "rescheduled"
            'deleted_at' => null, // Soft delete timestamp (null if not deleted)
            'created_at' => '2025-03-20 09:15:00+00', // Creation timestamp with timezone
            'updated_at' => '2025-03-20 09:15:00+00'
        ]);

        MeetingDetail::create([ // Auto-incremented ID
            'arrange_date' => '2025-03-25 14:30:00+00', // Timezone-aware timestamp
            'meeting_type' => 'online', // Either 'online' or 'offline'
            'location' => null, // Location for offline meetings, null for online
            'meeting_link' => 'https://zoom.us/j/123456789', // Link for online meetings, null for offline
            'online_meeting_application_type' => 'Zoom', // Application used for online meeting
            'arrange_id' => 2, // Foreign key to arrangings table
            'topic' => 'Project Kickoff Meeting',
            'description' => 'Initial discussion about project scope and timeline', // Optional description
            'status' => 'cancelled', // One of: "cancelled", "pending", "completed", "rescheduled"
            'deleted_at' => null, // Soft delete timestamp (null if not deleted)
            'created_at' => '2025-03-20 09:15:00+00', // Creation timestamp with timezone
            'updated_at' => '2025-03-20 09:15:00+00'
        ]);

        MeetingDetail::create([ // Auto-incremented ID
            'arrange_date' => '2025-03-25 14:30:00+00', // Timezone-aware timestamp
            'meeting_type' => 'online', // Either 'online' or 'offline'
            'location' => null, // Location for offline meetings, null for online
            'meeting_link' => 'https://zoom.us/j/123456789', // Link for online meetings, null for offline
            'online_meeting_application_type' => 'Zoom', // Application used for online meeting
            'arrange_id' => 1, // Foreign key to arrangings table
            'topic' => 'Project Kickoff Meeting',
            'description' => 'Initial discussion about project scope and timeline', // Optional description
            'status' => 'cancelled', // One of: "cancelled", "pending", "completed", "rescheduled"
            'deleted_at' => null, // Soft delete timestamp (null if not deleted)
            'created_at' => '2025-03-20 09:15:00+00', // Creation timestamp with timezone
            'updated_at' => '2025-03-20 09:15:00+00'
        ]);
    }
}
