<?php
namespace Database\Seeders;

use App\Models\Arranging;
use App\Models\MeetingDetail;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DashboardTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create an arranging record for student_id = 1 and tutor_id = 1 with a valid status.
        $arranging = Arranging::create([
            'student_id' => 1,
            'tutor_id'   => 1,
            'status'     => 'pending', // Allowed values: pending, completed, canceled.
        ]);

        // Create an upcoming online meeting (2 days in the future).
        MeetingDetail::create([
            'arrange_date'                    => Carbon::now()->addDays(2),
            'meeting_type'                    => 'online', // Allowed values: online, offline.
            'location'                        => null,     // For online meetings, location can be null.
            'meeting_link'                    => 'https://example.com/meeting-online',
            'online_meeting_application_type' => 'zoom',
            'arrange_id'                      => $arranging->id,
            'topic'                           => 'Upcoming Online Meeting',
            'description'                     => 'This is a test online meeting scheduled in the future.',
            'status'                          => 'pending', // Allowed statuses: cancelled, pending, completed, rescheduled.
        ]);

        // Create an upcoming offline meeting (represents a campus meeting) scheduled 3 days in the future.
        MeetingDetail::create([
            'arrange_date'                    => Carbon::now()->addDays(3),
            'meeting_type'                    => 'offline', // Using "offline" instead of "campus" per migration.
            'location'                        => 'Campus Room A',
            'meeting_link'                    => null,
            'online_meeting_application_type' => null,
            'arrange_id'                      => $arranging->id,
            'topic'                           => 'Upcoming Offline (Campus) Meeting',
            'description'                     => 'This is a test offline meeting scheduled in the future.',
            'status'                          => 'pending',
        ]);

        // Optional: Create a past meeting (1 day in the past) to ensure it's not picked up as upcoming.
        MeetingDetail::create([
            'arrange_date'                    => Carbon::now()->subDays(1),
            'meeting_type'                    => 'online',
            'location'                        => null,
            'meeting_link'                    => 'https://example.com/past-meeting',
            'online_meeting_application_type' => 'teams',
            'arrange_id'                      => $arranging->id,
            'topic'                           => 'Past Meeting',
            'description'                     => 'This meeting was held in the past and should not appear as upcoming.',
            'status'                          => 'completed',
        ]);
    }
}
