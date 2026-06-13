<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaService
{
    public function upload(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($directory, $filename, $disk);

        Media::create([
            'filename'      => $filename,
            'original_name' => $file->getClientOriginalName(),
            'path'          => $path,
            'disk'          => $disk,
            'mime_type'     => $file->getMimeType(),
            'size'          => $file->getSize(),
            'folder'        => $directory,
        ]);

        return $path;
    }

    public function delete(string $path, string $disk = 'public'): bool
    {
        Media::where('path', $path)->delete();
        return Storage::disk($disk)->delete($path);
    }

    public function exists(string $path, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->exists($path);
    }

    public function url(string $path, string $disk = 'public'): string
    {
        return Storage::disk($disk)->url($path);
    }
}
