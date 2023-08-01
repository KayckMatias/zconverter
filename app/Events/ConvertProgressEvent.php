<?php

namespace App\Events;

use App\Models\ConvertStatus;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConvertProgressEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    public function __construct(public ConvertStatus $convert_status)
    {
        //
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'Converter.Progress';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('Converter.Progress.' . $this->convert_status->file->user_id),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $item = $this->convert_status;

        return [
            "id" => $item->id,
            "file_id" => $item->file->id,
            "status" => $item->status ?? 'No info',
            "progress" => $item->progress ?? 'No info',
            "last_update" => $item->updated_at ?? 'No info',
        ];
    }
}
