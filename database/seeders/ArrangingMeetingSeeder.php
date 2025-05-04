<?php
namespace Database\Seeders;

use App\Models\Arranging;
use App\Models\MeetingDetail;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ArrangingMeetingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create an Arranging record linking student 15 and tutor 3.
        $arranging = Arranging::create([
            'student_id' => 15,
            'tutor_id'   => 3,
            'status'     => 'pending', // Adjust status as needed.
        ]);

        // Create an online MeetingDetail record for this arranging.
        MeetingDetail::create([
            'arrange_date'                    => Carbon::now(),
            'meeting_type'                    => 'online',
            'location'                        => null,
            'meeting_link'                    => 'https://example.com/online-meeting',
            'online_meeting_application_type' => 'zoom',
            'arrange_id'                      => $arranging->id,
            'topic'                           => 'Online Meeting Discussion',
            'status'                          => 'pending',
        ]);

        // Create a campus MeetingDetail record for this arranging.
        MeetingDetail::create([
            'arrange_date'                    => Carbon::now(),
            'meeting_type'                    => 'campus', // Use 'campus' (or 'offline' if that fits your enum)
            'location'                        => 'campus',
            'meeting_link'                    => 'https://example.com/online-meeting',
            'online_meeting_application_type' => "zoom",
            'arrange_id'                      => $arranging->id,
            'topic'                           => 'Campus Meeting',
            'status'                          => 'completed',
        ]);
    }
}
