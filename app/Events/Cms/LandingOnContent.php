<?php

namespace App\Events\Cms;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

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
