<?php
namespace App\Http\Controllers;

use App\Models\Allocation;
use App\Models\Blog;
use App\Models\BlogDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($studentId)
    {
        $blogs = Blog::with(['comments', 'comments.tutor', 'comments.student', 'documents'])
            ->where('student_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($blogs->isEmpty()) {
            return response()->json(['message' => 'No blogs found for this student.', 'status' => 404]);
        }

        return response()->json(['data' => $blogs, 'status' => 200, 'message' => 'Blog fetch successfully'], 200);
    }

    /**
     * Store a newly created blog and its documents in storage.
     */
    public function store(Request $request)
    {

        ob_clean();
        $user = Auth::user();

        if (! $user || ! $user->can('manage blog')) {
            return response()->json(['error' => 'Only tutors and students can create blogs.'], 403);
        }

        try {

            $validatedData = $request->validate([
                'title'       => 'required|string|max:255',
                'student_id'  => 'nullable|integer',
                'content'     => 'required',
                'documents'   => 'nullable|array',
                'documents.*' => 'file|mimes:pdf,doc,docx,txt',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'errors'  => $e->errors(),
                'message' => 'Enter inputs in required fields!',
            ], 422);
        }

        if ($user->hasRole('student')) {
            $allocation = Allocation::where('student_id', $user->id)->first();
            if (! $allocation) {
                return response()->json(['error' => 'No allocation found for this student', 'status' => 404], 404);
            }

            //dd($allocation->tutor_id);
            $validatedData['student_id']  = $user->id;
            $validatedData['tutor_id']    = $allocation->tutor_id;
            $validatedData['author']      = $user->name;
            $validatedData['author_role'] = 'student';
        } elseif ($user->hasRole('tutor')) {
            // $allocation = Allocation::where('tutor_id', $user->id)->first();
            // if (! $allocation) {
            //     return response()->json(['error' => 'No allocation found for this tutor'], 404);
            // }
            $validatedData['tutor_id'] = $user->id;
            //  $validatedData['student_id']  = $allocation->student_id;
            $validatedData['author']      = $user->name;
            $validatedData['author_role'] = 'tutor';
        } else {
            return response()->json(['error' => 'Only tutors and students can create blogs.', 'status' => 403], 403);
        }

        DB::beginTransaction();
        try {

            $blog = Blog::create($validatedData);

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {

                    $path = $document->store('documents', 'public');

                    BlogDocument::create([
                        'blog_id'          => $blog->id,
                        'BlogDocumentFile' => $path,
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error saving blog: ' . $e->getMessage(), 'status' => 500], 500);
        }

        return response()->json(['message' => 'Blog and documents saved successfully!', 'status' => 201], 201);
    }

    /**
     * Display the specified blog along with its documents.
     */
    public function show(string $id)
    {
        // Retrieve the blog along with its related documents and comments
        $blog = Blog::with(['documents', 'comments'])->find($id);

        if (! $blog) {
            return response()->json(['error' => 'Blog not found.', 'status' => 404], 404);
        }

        return response()->json(['data' => $blog, 'status' => 200, 'message' => 'Blog fetch successfully'], 200);
    }

    /**
     * Update the specified blog and add new documents if provided.
     */
    public function update(Request $request, string $id)
    {

        ob_clean();
        $user = Auth::user();

        if (! $user || ! $user->can('manage blog')) {
            return response()->json(['error' => 'Only tutors and students can update blogs.'], 403);
        }

        $blog = Blog::find($id);

        if (! $blog) {
            return response()->json(['error' => 'Blog not found.', 'status' => 404], 404);
        }

        if ($user->hasRole('student') && $blog->student_id !== $user->id) {
            return response()->json(['error' => 'You are not allowed to update this blog.', 'status' => 403], 403);
        }
        if ($user->hasRole('tutor') && $blog->tutor_id !== $user->id) {
            return response()->json(['error' => 'You are not allowed to update this blog.', 'status' => 403], 403);
        }

        try {

            $validatedData = $request->validate([
                'title'       => 'sometimes|required|string|max:255',
                'content'     => 'sometimes|required',
                'documents'   => 'nullable|array',
                'documents.*' => 'file|mimes:pdf,doc,docx,txt',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors(), 'status' => 422], 422);
        }

        DB::beginTransaction();
        try {

            $result = $blog->update($validatedData);

            if ($result) {

                if ($request->hasFile('documents')) {

                    foreach ($blog->documents as $existingDocument) {
                        Storage::disk('public')->delete($existingDocument->BlogDocumentFile);
                        $existingDocument->delete();
                    }

                    foreach ($request->file('documents') as $document) {
                        $path = $document->store('documents', 'public');

                        BlogDocument::create([
                            'blog_id'          => $blog->id,
                            'BlogDocumentFile' => $path,
                        ]);
                    }
                }

            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error updating blog: ' . $e->getMessage(), 'status' => 500], 500);
        }

        return response()->json(['message' => 'Blog updated successfully!', 'status' => 200], 200);
    }

    /**
     * Remove the specified blog and its documents from storage.
     */
    public function destroy(Request $request, string $id)
    {
        ob_clean();
        $user = Auth::user();

        if (! $user || ! $user->can('manage blog')) {
            return response()->json(['error' => 'Only tutors and students can delete blogs.', 'status' => 403], 403);
        }

        $blog = Blog::find($id);
        if (! $blog) {
            return response()->json(['error' => 'Blog not found.', 'status' => 404], 404);
        }

        if ($user->hasRole('student') && $blog->student_id !== $user->id) {
            return response()->json(['error' => 'You are not allowed to delete this blog.', 'status' => 403], 403);
        }
        if ($user->hasRole('tutor') && $blog->tutor_id !== $user->id) {
            return response()->json(['error' => 'You are not allowed to delete this blog.', 'status' => 403], 403);
        }

        DB::beginTransaction();
        try {

            foreach ($blog->documents as $document) {
                Storage::disk('public')->delete($document->BlogDocumentFile);
                $document->delete();
            }

            $result = $blog->delete();

            //dd($result);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error deleting blog: ' . $e->getMessage(), 'status' => 500], 500);
        }

        return response()->json(['message' => 'Blog deleted successfully!', 'status' => 200], 200);
    }

}
