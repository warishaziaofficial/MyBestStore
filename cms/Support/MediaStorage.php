<?php

namespace Cms\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class MediaStorage
{
    public static function store(UploadedFile $file): array
    {
        $directory = 'uploads/cms/'.date('Y/m');
        $absoluteDirectory = public_path($directory);

        if (! is_dir($absoluteDirectory)) {
            mkdir($absoluteDirectory, 0755, true);
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $storedName = Str::uuid()->toString().'.'.$extension;
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getMimeType() ?: 'application/octet-stream';
        $file->move($absoluteDirectory, $storedName);

        $path = $directory.'/'.$storedName;

        return [
            'filename' => $originalName,
            'path' => $path,
            'mime_type' => $mimeType,
            'size_bytes' => (int) filesize(public_path($path)),
        ];
    }

    public static function deleteFile(?string $path): void
    {
        if (! $path || self::isRemoteUrl($path)) {
            return;
        }

        $absolute = public_path(ltrim($path, '/'));

        if (is_file($absolute)) {
            unlink($absolute);
        }
    }

    public static function isRemoteUrl(?string $path): bool
    {
        return is_string($path) && (str_starts_with($path, 'http://') || str_starts_with($path, 'https://'));
    }

    public static function exists(?string $path): bool
    {
        if (empty($path) || self::isRemoteUrl($path)) {
            return ! empty($path);
        }

        return is_file(public_path(ltrim($path, '/')));
    }
}
