<?php
namespace Database\Seeders;

use App\Models\Arranging;
use App\Models\MeetingDetail;
use App\Models\MeetingRequest;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeetingRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        MeetingRequest::truncate();
        MeetingDetail::truncate();
        Arranging::truncate();

        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $arranging = Arranging::create([
            'student_id' => 2,
            'tutor_id'   => 1,
            'status'     => 'pending',
        ]);

        $meetingDetail = MeetingDetail::create([
            'arrange_date' => Carbon::now('UTC')->toDateString(),
            'meeting_type' => 'online',
            'topic'        => 'Sample Meeting Topic',
            'location'     => 'Zoom',
            // 'online_meeting_applicaiton' => 'Zoom', // removed this field
            'arrange_id'   => $arranging->id,
            'status'       => 'pending',
            'meeting_link' => null,
            'description'  => null,
        ]);

        MeetingRequest::create([
            'student_id'                 => 2,
            'arrange_date'               => Carbon::now('UTC')->toDateString(),
            'tutor_id'                   => 1,
            'reason'                     => 'Need help with assignment',
            'topic'                      => $meetingDetail->topic,
            'meeting_type'               => 'online',
            'location'                   => 'Zoom',
            'online_meeting_applicaiton' => 'Zoom',
            'approved'                   => false,
            'status'                     => 'pending',
            'approved_arrange_id'        => $arranging->id,
        ]);

        // Create a cancelled MeetingRequest.
        MeetingRequest::create([
            'student_id'                 => 2,
            'arrange_date'               => Carbon::now('UTC')->toDateString(),
            'tutor_id'                   => 1,
            'reason'                     => 'No longer needed',
            'topic'                      => 'Sample Meeting Topic',
            'meeting_type'               => 'online',
            'location'                   => 'Zoom',
            'online_meeting_applicaiton' => 'Zoom',
            'approved'                   => false,
            'status'                     => 'cancelled',
            'approved_arrange_id'        => $arranging->id,
        ]);

        // Create a rejected MeetingRequest.
        MeetingRequest::create([
            'student_id'                 => 2,
            'arrange_date'               => Carbon::now('UTC')->toDateString(),
            'tutor_id'                   => 1,
            'reason'                     => 'Not relevant at this time',
            'topic'                      => 'Sample Meeting Topic',
            'meeting_type'               => 'online',
            'location'                   => 'Zoom',
            'online_meeting_applicaiton' => 'Zoom',
            'approved'                   => false,
            'status'                     => 'reject',
            'approved_arrange_id'        => $arranging->id,
        ]);

        // Create an approved MeetingRequest.
        MeetingRequest::create([
            'student_id'                 => 2,
            'arrange_date'               => Carbon::now('UTC')->toDateString(),
            'tutor_id'                   => 1,
            'reason'                     => 'Request approved for meeting',
            'topic'                      => 'Sample Meeting Topic',
            'meeting_type'               => 'online',
            'location'                   => 'Zoom',
            'online_meeting_applicaiton' => 'Zoom',
            'approved'                   => true,
            'status'                     => 'approved',
            'approved_arrange_id'        => $arranging->id,
        ]);
    }
}
