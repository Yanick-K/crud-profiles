<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $exceptColumns = ['created_at', 'updated_at'];

        if (!auth('sanctum')->check()) {
             $exceptColumns[] = 'status';
        }

        return collect(parent::toArray($request))->except($exceptColumns)->toArray();
    }
}
