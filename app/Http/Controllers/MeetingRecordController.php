<?php
namespace App\Http\Controllers;

use App\Models\MeetingRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MeetingRecordController extends Controller
{
    /**
     * Create a new meeting record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function store(Request $request)
    {
        ob_clean();
        $user = Auth::user();

        if (! $user || ! $user->hasRole('tutor')) {
            return response()->json(['error' => 'Only tutors can create meeting record.'], 403);
        }

        // Validate the request data.
        $validatedData = $request->validate([
            'meeting_detail_id' => 'required|integer|exists:meeting_detail,id',
            'meeting_note'      => 'nullable|string',
            'uploaded_document' => 'nullable|file',
        ]);

        // Handle file upload if present.
        if ($request->hasFile('uploaded_document')) {
            $path                               = $request->file('uploaded_document')->store('uploads', 'public');
            $validatedData['uploaded_document'] = $path;
        }

        // Use transaction to ensure atomicity.
        DB::beginTransaction();
        try {
            // Create the meeting record.
            $meetingRecord = MeetingRecord::create($validatedData);

            // Use the relationship to get the associated meeting detail.
            $meetingDetail = $meetingRecord->meetingDetail;
            if ($meetingDetail) {
                // Update the meeting detail's status.
                $meetingDetail->status = 'completed';
                $meetingDetail->save();

                // Using the meeting detail's arranging relationship, update the arranging status.
                $arranging = $meetingDetail->arranging;
                if ($arranging) {
                    $arranging->status = 'completed';
                    $arranging->save();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error processing meeting record.'], 500);
        }

        return response()->json([
            'message' => 'Meeting record saved successfully',
            'status'  => '201',
        ]);
    }

    /**
     * Show the specified meeting record.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = Auth::user();

        // Check if the authenticated user has permission to create a blog
        if (! $user) {
            return response()->json(['error' => 'Only authenticated user can view this!'], 403);
        }

        $meetingRecord = MeetingRecord::findOrFail($id);
        return response()->json(['message' => $meetingRecord, 'status' => '200']);
    }

    /**
     * Update the specified meeting record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        ob_clean();
        $user = Auth::user();

        // Check if the authenticated user has permission to create a blog
        if (! $user || ! $user->hasRole('tutor')) {
            return response()->json(['error' => 'Only tutors can update meeting record.'], 403);
        }
        $meetingRecord = MeetingRecord::findOrFail($id);

        // Validate the request data.
        $validatedData = $request->validate([

            'meeting_note'      => 'sometimes|required|string',
            'uploaded_document' => 'nullable|file',
        ]);

        // Check if a new file is provided.
        if ($request->hasFile('uploaded_document')) {
            // If an existing document is already stored, delete it.
            if ($meetingRecord->uploaded_document && Storage::disk('public')->exists($meetingRecord->uploaded_document)) {
                Storage::disk('public')->delete($meetingRecord->uploaded_document);
            }
            // Store the new file.
            $path                               = $request->file('uploaded_document')->store('uploads', 'public');
            $validatedData['uploaded_document'] = $path;
        }

        // Update the meeting record.
        $meetingRecord->update($validatedData);

        return response()->json(['message' => 'Meeting record update successfully!', 'status' => 200]);
    }

    /**
     * Soft delete the specified meeting record and remove the associated file.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {

        $user = Auth::user();

        // Check if the authenticated user has permission to create a blog
        if (! $user || ! $user->hasRole('tutor')) {
            return response()->json(['error' => 'Only tutors can delete meeting record.'], 403);
        }
        $meetingRecord = MeetingRecord::findOrFail($id);

        // dd($meetingRecord);

        // Delete the file if it exists.
        if ($meetingRecord->uploaded_document && Storage::disk('public')->exists($meetingRecord->uploaded_document)) {
            Storage::disk('public')->delete($meetingRecord->uploaded_document);
        }

        // Soft delete the record.
        $meetingRecord->delete();

        return response()->json(['message' => 'Meeting record deleted successfully.', 'status' => 200]);
    }
}
