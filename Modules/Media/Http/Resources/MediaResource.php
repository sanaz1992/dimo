<?php

namespace Modules\Media\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $sizes = $this->thumbnail_path ?? [];
        if (is_string($sizes)) {
            dd($sizes);
        }
        $sizes = array_map(fn ($size) => Storage::disk($this->disk)->url($size), $sizes);

        return [
            'collection' => $this->collection,
            'sizes' => $sizes,
        ];
    }
}
