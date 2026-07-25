<?php

namespace App\Services;

use App\Models\CarouselSlide;
use App\Support\AuditLogger;
use Illuminate\Database\Eloquent\Collection;

class CarouselService
{
    public function listAllOrdered(): Collection
    {
        return CarouselSlide::orderBy('sort_order')->orderBy('id')->get();
    }

    public function create(array $data): CarouselSlide
    {
        $slide = CarouselSlide::create($data);
        AuditLogger::record('carousel.create', $slide);

        return $slide;
    }

    public function update(CarouselSlide $slide, array $data): CarouselSlide
    {
        $slide->update($data);
        AuditLogger::record('carousel.update', $slide);

        return $slide;
    }

    public function delete(CarouselSlide $slide): void
    {
        AuditLogger::record('carousel.delete', $slide);
        $slide->delete();
    }

    public function toggle(CarouselSlide $slide): CarouselSlide
    {
        $slide->update(['is_active' => ! $slide->is_active]);
        AuditLogger::record('carousel.toggle', $slide);

        return $slide;
    }

    public function activeForHome(): Collection
    {
        return CarouselSlide::active()->orderBy('sort_order')->get();
    }
}
