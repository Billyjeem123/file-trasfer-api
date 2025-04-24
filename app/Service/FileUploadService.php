<?php

namespace App\Service;

use App\Models\UploadSession;
use App\Models\UploadFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Mail;
use App\Mail\UploadNotificationMail;
use Carbon\Carbon;

class FileUploadService
{

    public function handleUpload(array $validated, array $files): array {
        $token = $this->token();
        $expiresInDays = $validated['expires_in'] ?? 1; #One happens to be the default value, Meaning, to expire next day after current date
        $email = $validated['email_to_notify'] ?? null;

        $session = UploadSession::create([
            'token' => $token,
            'email_to_notify' => $email,
            'expires_at' => now()->addDays($expiresInDays),
        ]);

        foreach ($files as $file) {
            $path = $file->store("uploads/{$token}", 'local');
            UploadFile::create([
                'upload_session_id' => $session->id,
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
            ]);
        }

        $downloadLink = url("/api/download/{$token}");

        try {
            if ($email) {
                Mail::to($email)->send(new UploadNotificationMail($downloadLink));
                $session->update(['notified' => true]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send upload notification email: ' . $e->getMessage());
        }

        return ['token' => $token];
    }

    public  function token($length = 6): string
    {
        return str_pad(rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }
}
