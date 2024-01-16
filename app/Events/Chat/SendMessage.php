<?php

namespace App\Events\Chat;

use App\Models\MensagensModel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $mensagem;
    public $userNotifi;

    /**
     * Create a new event instance.
     */
    public function __construct(MensagensModel $mensagem, int $userNotifi)
    {
        $this->mensagem = $mensagem;
        $this->userNotifi = $userNotifi;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("user.{$this->userNotifi}"),
        ];
    }

    public function broadcastAs()
    {
        return 'SendMessage';
    }
    public function broadcastWith()
    {
        return  [
            'menssage' => $this->mensagem
        ];
    }
}
