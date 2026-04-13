<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'is_draft' => (bool) $this->is_draft,
            'published_at' => $this->published_at,
            'author' => $this->user->name, // Requirement 4-1: Include author data
            'created_at' => $this->created_at,
        ];
    }
}
