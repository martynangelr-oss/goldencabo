<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Support\Str;

trait HandlesFileStorage
{
    private function docRootPath(string $relativePath): string
    {
        $base = rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/');
        return $base . '/storage/' . ltrim($relativePath, '/');
    }

    private function saveFile(\Illuminate\Http\UploadedFile $file, string $folder): string
    {
        $dir = $this->docRootPath($folder);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $filename = Str::random(40) . '.' . strtolower($file->getClientOriginalExtension());
        $file->move($dir, $filename);
        return $folder . '/' . $filename;
    }

    private function deleteFile(?string $path): void
    {
        if (!$path || str_starts_with($path, 'http')) {
            return;
        }
        $full = $this->docRootPath($path);
        if (file_exists($full)) {
            unlink($full);
        }
    }
}
