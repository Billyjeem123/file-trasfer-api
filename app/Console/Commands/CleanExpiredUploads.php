<?php

namespace App\Console\Commands;

use App\Models\UploadSession;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanExpiredUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:expired-uploads';
    protected $description = 'Deletes expired upload files and sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    /**
     * Execute the console command.
     */
    public function handle()
{
    $expiredSessions = UploadSession::where('expires_at', '<', now())->get();

    foreach ($expiredSessions as $session) {
        foreach ($session->files as $file) {
            Storage::disk('local')->delete($file->file_path);
        }
        $session->delete();
    }

    $this->info('Expired uploads cleaned up.');
}

}
