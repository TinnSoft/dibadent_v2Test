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
    
    public $detail, $model, $route, $notify, $value;
    
    /**
     * Create a new event instance.
     * $value: who must read the message
     * @return void
     */
    public function __construct($detail, $model, $route, $notify, $value=null)
    {
        $this->detail = $detail;
        $this->model = $model;
        $this->route = $route;
        $this->notify = $notify;
        $this->value = $value;
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
