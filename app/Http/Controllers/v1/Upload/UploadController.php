<?php

namespace App\Http\Controllers\v1\Upload;

use App\Http\Controllers\Controller;
use App\Service\FileUploadService;
use App\Models\UploadSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class UploadController extends Controller
{

    protected $fileService;

    public function __construct(FileUploadService $fileService)
    {
        $this->fileService = $fileService;
    }
    public function upload(Request $request)
    {
        $validated = $request->validate([
            'files' => 'required|array|max:5',
            'files.*' => 'file|max:102400|mimes:jpg,png,pdf,docx,zip',
            'expires_in' => 'nullable|integer|min:1|max:30',
            'email_to_notify' => 'nullable|email',
        ]);

        $data = $this->fileService->handleUpload($validated, $request->file('files'));

        return response()->json([
            'success' => true,
            'download_link' => url('/api/download/' . $data['token']),
        ]);
    }
    public function download($token)
    {
       # Get session and check expiration
        $session = UploadSession::with('files')->where('token', $token)->first();

        if (!$session) {
            return response()->json(['error' => 'Invalid or expired download link.'], 404);
        }

        if (now()->greaterThan($session->expires_at)) {
            return response()->json(['error' => 'This link has expired.'], 410);
        }

        $files = $session->files;

        if ($files->count() === 1) {
           # Single file download
            $file = $files->first();
            $file->increment('downloads');

            return Storage::disk('local')->download($file->file_path, $file->original_name);
        }

       # Multiple files: Zip and download
        $zipFileName = "files-{$token}.zip";
        $zipPath = storage_path("app/temp/{$zipFileName}");

       # Ensure temp directory exists
        if (!Storage::exists('temp')) {
            Storage::makeDirectory('temp');
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($files as $file) {
                $file->increment('downloads');
                $filePath = storage_path("app/" . $file->file_path);
                $zip->addFile($filePath, $file->original_name);
            }
            $zip->close();

            return response()->download($zipPath)->deleteFileAfterSend(true);
        }

        return response()->json(['error' => 'Failed to create download package.'], 500);
    }

    public function stats($token): \Illuminate\Http\JsonResponse
    {
        $session = UploadSession::where('token', $token)->with('files')->firstOrFail();
        return response()->json([
            'expires_at' => $session->expires_at,
            'files' => $session->files->map(fn($file) => [
                'name' => $file->original_name,
                'downloads' => $file->downloads,
                'size' => $this->formatBytes(Storage::size($file->file_path)),
            ]),
        ]);

    }



    /**
     * Convert bytes to human-readable format (e.g., KB, MB).
     */
    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }


}
