<?php

namespace App\Events\Cms;

use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LandingOnContent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $slug;
    public $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(string $slug, ?int $userId = null)
    {
        $this->slug = $slug;
        $this->userId = $userId;
    }
}
