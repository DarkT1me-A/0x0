<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public function guide()
    {
        $content = <<<EOT
Minimalistic file upload service (like 0x0.st).

Upload:
  curl -F"file=@filename" https://yourdomain.com -v

Response example headers:
  HTTP/1.1 200 OK
  X-Delete: https://yourdomain.com/delete/DELETE_HASH

Response body:
  https://yourdomain.com/file/HASH

Download:
  curl https://yourdomain.com/file/HASH

Delete:
  curl https://yourdomain.com/delete/DELETE_HASH
EOT;

        return response($content, 200)->header('Content-Type', 'text/plain');
    }

    public function upload(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response('File required', 400);
        }

        $file = $request->file('file');
        if (!$file->isValid()) {
            return response('Upload error', 400);
        }

        // Генерация имени файла и хэшей
        $randomName = Str::random(16);
        $extension = $file->getClientOriginalExtension();
        $filename = $randomName . ($extension ? '.' . $extension : '');
        $deleteHash = Str::random(40);

        // Хранение файла
        $path = $file->storeAs('uploads', $filename);

        // Время жизни файла: пример простая формула ~24 часа
        $expiresAt = now()->addHours(24);

        // Сохранение в БД
        File::create([
            'hash' => $randomName,
            'delete_hash' => $deleteHash,
            'filename' => $filename,
            'expires_at' => $expiresAt,
        ]);

        $fileUrl = url("/file/{$randomName}");
        $deleteUrl = url("/delete/{$deleteHash}");

        return response($fileUrl, 200)->header('X-Delete', $deleteUrl);
    }

    public function download($hash)
    {
        $file = File::where('hash', $hash)->first();

        if (!$file || $file->expires_at->isPast()) {
            abort(404, 'File not found or expired');
        }

        $filepath = storage_path('app/uploads/' . $file->filename);

        if (!file_exists($filepath)) {
            abort(404, 'File not found');
        }

        return response()->download($filepath, null, [], null);
    }

    public function delete($deleteHash)
    {
        $file = File::where('delete_hash', $deleteHash)->first();

        if (!$file) {
            abort(404, 'File not found');
        }

        Storage::delete('uploads/' . $file->filename);
        $file->delete();

        return response('Deleted', 200)->header('Content-Type', 'text/plain');
    }
}
