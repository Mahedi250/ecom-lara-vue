<?php

namespace App\Traits;

trait HasSeoFields
{
    public function getSeoTitle(): string
    {
        return $this->meta_title ?? $this->name ?? $this->title ?? '';
    }

    public function getSeoDescription(): string
    {
        return $this->meta_description ?? $this->short_description ?? $this->description ?? '';
    }
}
