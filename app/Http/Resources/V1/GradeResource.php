<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'subject'      => $this->subject->name,
            'score'        => $this->total_score,
            'is_published' => $this->is_locked,
            'term'         => $this->whenLoaded('term', fn() => $this->term->name),
        ];
    }
}
