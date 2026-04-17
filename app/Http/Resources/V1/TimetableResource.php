<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimetableResource extends JsonResource
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
            'day' => $this->day_of_week,
            'subject' => $this->subject->name,
            'teacher' => $this->teacher->name,
            'class' => $this->section->classLevel->name,
            'section' => $this->section->name,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ];
    }
}
