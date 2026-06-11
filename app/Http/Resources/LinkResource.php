<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LinkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'short_code' => $this->short_code,
            'short_url' => url('/' . $this->short_code),
            'original_url' => $this->original_url,
            'expires_at' => $this->expires_at,
            'clicks_count' => $this->whenCounted('clicks'),
            'created_at' => $this->created_at,
        ];
    }
}
