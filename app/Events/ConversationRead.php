<?php

namespace App\Events;

use App\Models\Conversation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ConversationRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public Conversation $conversation;

    /**
     * Create a new event instance.
     *
     * @param int|string $userId
     * @param Conversation $conversation
     * @return void
     */
    public function __construct($userId, Conversation $conversation)
    {
        $this->userId = $userId;
        $this->conversation = $conversation;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->userId);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'ConversationRead';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        // Eager load relationships to prevent extra database queries.
        $this->conversation->load(['participants', 'latestMessage.readReceipts']);

        // The user receiving this notification.
        $recipientId = $this->userId;

        // The other participant (the one who just read the message).
        $reader = $this->conversation->participants->where('id', '!=', $recipientId)->first();

        $lastMessage = $this->conversation->latestMessage;
        $preview = 'No messages yet.';

        if ($lastMessage) {
            switch ($lastMessage->type) {
                case 'image':
                    $preview = 'Photo';
                    break;
                case 'voice':
                    $preview = 'Voice Message';
                    break;
                case 'location':
                    $preview = 'Location';
                    break;
                case 'text':
                    $preview = Str::limit($lastMessage->content['text'] ?? '', 50);
                    break;
                default:
                    $preview = 'New Message';
            }
        }

        $seenAt = null;
        // Logic: Show a "seen" status if the last message was sent by the recipient of this event,
        // and the other participant (the reader) has a read receipt for it.
        if ($lastMessage && $reader && $lastMessage->sender_id == $recipientId) {
            $receipt = $lastMessage->readReceipts->firstWhere('user_id', $reader->id);
            if ($receipt) {
                $seenAt = $receipt->read_at;
            }
        }

        $payload = [
            'id' => $this->conversation->id,
            'last_message_at' => $this->conversation->last_message_at ? $this->conversation->last_message_at->toIso8601String() : null,
            'participant' => $reader ? [
                'id' => $reader->id,
                'name' => $reader->name,
                'avatar' => $reader->avatar,
                'type' => $reader->type,
            ] : null,
            'last_message' => $lastMessage ? [
                'preview' => $preview,
                'type' => $lastMessage->type,
                'sender_id' => $lastMessage->sender_id,
            ] : null,
            'latest_message' => $lastMessage,
            'seen_at' => $seenAt ? $seenAt->toIso8601String() : null,
        ];

        return ['conversation' => $payload];
    }
}