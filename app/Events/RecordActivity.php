<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;


class RecordActivity
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $detail, $model, $route;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($detail, $model, $route)
    {
        $this->detail = $detail;
        $this->model = $model;
        $this->route = $route;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
