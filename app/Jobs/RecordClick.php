<?php

namespace App\Jobs;

use App\Models\Click;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RecordClick implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $linkId,
        public string $clickedAt,
        public ?string $referrer,
        public ?string $userAgent,
        public ?string $ipHash,
    ) {}

    public function handle(): void
    {
        Click::create([
            'link_id' => $this->linkId,
            'clicked_at' => $this->clickedAt,
            'referrer' => $this->referrer,
            'user_agent' => $this->userAgent,
            'ip_hash' => $this->ipHash,
        ]);
    }
}
