<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\MeetingRecord;
use App\Models\BlogDocument;
use App\Models\Document;

class FileDownloadController extends Controller
{
    public function download($type, $id)
    {
        switch ($type) {
            case 'meeting':
                $record = MeetingRecord::find($id);
                $path = $record?->uploaded_document;
                $name = basename($path);
                break;

            case 'blog':
                $record = BlogDocument::find($id);
                $path = $record?->BlogDocumentFile;
                $name = basename($path);
                break;

            case 'general':
                $record = Document::find($id);
                $path = $record?->file;
                $name = $record?->file_name ?? basename($path);
                break;

            default:
                return response()->json(['message' => 'Invalid file type.'], 400);
        }

        $fullPath = storage_path("app/public/{$path}");

        if (!$path || !File::exists($fullPath)) {
            return response()->json(['message' => 'File not found.'], 404);
        }

        return response()->download($fullPath, $name, ['Content-Type' => 'application/octet-stream']);
    }


    public function downloadAllFilesForBlog($blogId)
    {
        $documents = BlogDocument::where('blog_id', $blogId)->get();

        if ($documents->isEmpty()) {
            return response()->json(['message' => 'No documents found for this blog.'], 404);
        }

        $zipFileName = "blog_{$blogId}_attachments_" . now()->format('Ymd_His') . '.zip';
        $zipPath = storage_path("app/public/{$zipFileName}");

        $zip = new \ZipArchive;
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            foreach ($documents as $doc) {
                $filePath = storage_path("app/public/" . $doc->BlogDocumentFile);
                if (File::exists($filePath)) {
                    $zip->addFile($filePath, basename($filePath));
                }
            }
            $zip->close();
        } else {
            return response()->json(['message' => 'Failed to create ZIP file.'], 500);
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
