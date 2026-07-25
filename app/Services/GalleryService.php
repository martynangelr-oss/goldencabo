<?php

namespace App\Services;

use App\Models\GalleryImage;
use App\Support\AuditLogger;
use Illuminate\Database\Eloquent\Collection;

class GalleryService
{
    public function listAllOrdered(): Collection
    {
        return GalleryImage::orderBy('sort_order')->orderBy('id')->get();
    }

    public function maxSortOrder(): int
    {
        return (int) (GalleryImage::max('sort_order') ?? 0);
    }

    public function create(array $data): GalleryImage
    {
        $image = GalleryImage::create($data);
        AuditLogger::record('gallery.create', $image);

        return $image;
    }

    public function update(GalleryImage $image, array $data): GalleryImage
    {
        $image->update($data);
        AuditLogger::record('gallery.update', $image);

        return $image;
    }

    public function delete(GalleryImage $image): void
    {
        AuditLogger::record('gallery.delete', $image);
        $image->delete();
    }

    public function toggle(GalleryImage $image): GalleryImage
    {
        $image->update(['is_active' => ! $image->is_active]);
        AuditLogger::record('gallery.toggle', $image);

        return $image;
    }

    public function activeForHome(): Collection
    {
        return GalleryImage::active()->orderBy('sort_order')->get();
    }
}
