<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $exceptColumns = ['id', 'created_at', 'updated_at'];

        if (!auth('sanctum')->check()) {
             $exceptColumns[] = 'status';
        }

        return collect(parent::toArray($request))->except($exceptColumns)->toArray();
    }
}
