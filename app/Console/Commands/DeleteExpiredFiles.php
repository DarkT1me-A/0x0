<?php

namespace App\Console\Commands;

use App\Models\File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteExpiredFiles extends Command
{
    protected $signature = 'files:cleanup';
    protected $description = 'Delete expired files';

    public function handle()
    {
        $expiredFiles = File::where('expires_at', '<', now())->get();

        foreach ($expiredFiles as $file) {
            Storage::delete('uploads/' . $file->filename);
            $file->delete();
            $this->info("Deleted file {$file->filename}");
        }

        return 0;
    }
}
