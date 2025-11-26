<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Events\ConversationRead;
use App\Events\ConversationUpdated;
use App\Events\MessageDeleted;
use App\Events\MessageRead;
use App\Events\MessageSent;
use App\Events\MessageUpdated;
use App\Events\UserTyping;
use App\Events\UserTypingInConversation;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Jobs\SendChatMessageNotifications;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageReadStatus;
use App\Models\User;
use App\Notifications\NewChatMessageNotification;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Kreait\Laravel\Firebase\Facades\Firebase;


class ChatController extends Controller
{
    /**
     * Display a listing of the user's conversations.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
		$userId = $user->id;

        $conversations = $user->conversations()
            ->with([
                'participants' => function ($query) use ($user) {
                    $query->where('user_id', '!=', $user->id);
                },
                'messages' => function ($query) {
                    $query->with('readReceipts.user')->latest()->limit(1);
                }
            ])
            ->orderBy('last_message_at', 'desc')
            ->get();

        return response()->json($conversations);
    }

    /**
     * Display the specified conversation's details and mark as read.
     */
    public function show(Conversation $conversation)
    {
        $user = Auth::user();
        if (!$conversation->participants->contains($user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        DB::table('conversation_participants')
            ->where('conversation_id', $conversation->id)
            ->where('user_id', $user->id)
            ->update(['last_read' => now()]);

        $conversation->load(['participants' => function ($query) use ($user) {
            $query->where('user_id', '!=', $user->id);
        }]);
        
        return response()->json($conversation);
    }

    /**
     * Get messages for a conversation with pagination and handle read receipts.
     */
    public function getMessages(Request $request, Conversation $conversation)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$conversation->participants->contains($user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($request->has('polling') && $request->boolean('polling')) {
            return $this->pollMessages($request, $conversation);
        }

		$this->markMessagesAsRead($conversation, $user);

        $messages = $conversation->messages()
            ->with(['sender', 'replyToMessage.sender', 'reactions', 'readReceipts.user'])
            ->latest()
            ->paginate(20);

        $messages->getCollection()->transform(fn($message) => $this->transformMessage($message));

        return response()->json($messages);
    }

    /**
     * Poll for new messages since a given timestamp (for fallback when WebSocket fails).
     */
    public function pollMessages(Request $request, Conversation $conversation)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$conversation->participants->contains($user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $since = $request->input('since');
        $limit = (int) $request->input('limit', 50);

        $query = $conversation->messages()
            ->with(['sender', 'replyToMessage.sender', 'reactions', 'readReceipts.user'])
            ->orderBy('created_at', 'asc');

        if ($since) {
            $query->where('created_at', '>', $since);
        }

        $messages = $query->limit($limit)->get();
        $messages->transform(fn($message) => $this->transformMessage($message));

        return response()->json(['data' => $messages]);
    }

    /**
     * Store a newly created message in storage.
     */
    public function store(Request $request, Conversation $conversation)
    {
        // 1. Authorization: Ensure the user is a participant of the conversation.
        if (!$conversation->participants->contains(Auth::user())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // 2. Validation: Validate the incoming request based on message type.
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:text,image,voice,location',
            'content' => 'nullable|string|max:5000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'voice' => 'nullable|file|mimetypes:audio/wav,audio/mpeg,audio/ogg,audio/webm,audio/mp4,video/mp4|max:10240',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'reply_to_message_id' => 'nullable|uuid|exists:messages,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $messageType = $request->input('type');
        $content = ['text' => $request->input('content')];

        // 3. Content Processing: Handle file uploads and location data.
        switch ($messageType) {
            case 'image':
                if (!$request->hasFile('image')) return response()->json(['error' => 'Image file is required.'], 422);
                $path = FileUploadService::storeFile($request->file('image'), 'chat_images');
                $content['image_path'] = $path;
                break;
            case 'voice':
                if (!$request->hasFile('voice')) return response()->json(['error' => 'Voice file is required.'], 422);
                $path = FileUploadService::storeFile($request->file('voice'), 'chat_voices');
                $content['voice_path'] = $path;
                break;
            case 'location':
                if (!$request->has('latitude') || !$request->has('longitude')) return response()->json(['error' => 'Latitude and longitude are required.'], 422);
                $content['latitude'] = $request->input('latitude');
                $content['longitude'] = $request->input('longitude');
                break;
            case 'text':
                if (!$request->filled('content')) return response()->json(['error' => 'Message content is required.'], 422);
                break;
        }
        
        // 4. Handle Reply-To Message
        $replyMessageId = null;
        if ($request->filled('reply_to_message_id')) {
            $parentMessage = Message::where('id', $request->reply_to_message_id)
                ->where('conversation_id', $conversation->id)->first();
            if ($parentMessage) {
                $replyMessageId = $parentMessage->id;
            }
        }

        // 5. Create the Message and update related records
        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'content'   => $content,
            'type'      => $messageType,
            'reply_to_message_id' => $replyMessageId
        ]);

        $message->readReceipts()->create(['user_id' => $user->id, 'read_at' => now()]);
        $conversation->update(['last_message_at' => now()]);
        DB::table('conversation_participants')->where('conversation_id', $conversation->id)->where('user_id', $user->id)->update(['last_read' => now()]);
        
        // Eager load relationships for the job and the response
        $freshMessage = $message->fresh(['sender', 'replyToMessage.sender', 'reactions', 'readReceipts.user']);
        $otherParticipants = $conversation->participants()->where('user_id', '!=', $user->id)->get();

        // 6. Dispatch the job to handle broadcasting and notifications
        SendChatMessageNotifications::dispatch($freshMessage, $otherParticipants);
        
        // 7. Return the newly created message immediately
        $transformedMessage = $this->transformMessage($freshMessage);
        
        return response()->json($transformedMessage, 201);
    }

    /**
     * Update an existing message (Edit).
     */
    public function update(Request $request, Message $message)
    {
        if ($message->sender_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), ['content' => 'required|string|max:5000']);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $content = $message->content;
        $content['text'] = $request->input('content');
        
        $message->update(['content' => $content, 'edited_at' => now()]);

        $freshMessage = $message->fresh(['sender', 'replyToMessage.sender', 'reactions', 'readReceipts.user']);
        
        broadcast(new MessageUpdated($freshMessage))->toOthers();

        $transformedMessage = $this->transformMessage($freshMessage);
        return response()->json($transformedMessage);
    }

    /**
     * Delete a message.
     */
    public function destroy(Message $message)
    {
        if ($message->sender_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $conversationId = $message->conversation_id;
        $messageId = $message->id;
        
        $message->delete();

        broadcast(new MessageDeleted($conversationId, $messageId))->toOthers();

        return response()->json(['message' => 'Message deleted successfully']);
    }
    
    /**
     * Reusable helper to format the message response.
     */
    private function transformMessage(Message $message): Message
    {
        $content = $message->content;
        if ($message->type === 'image' && isset($content['image_path'])) {
            $content['image_url'] = Helper::imageUrl($content['image_path']);
            unset($content['image_path']);
        }
        if ($message->type === 'voice' && isset($content['voice_path'])) {
            $content['voice_url'] = Helper::imageUrl($content['voice_path']);
            unset($content['voice_path']);
        }
        $message->content = $content;
        return $message;
    }

    /**
     * Handle user typing notifications.
     */
    public function typing(Request $request, Conversation $conversation)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$conversation->participants->contains($user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            // Event for the active chat window
            broadcast(new UserTyping($user, $conversation))->toOthers();
            // Event for the conversation lists of other participants
			$otherParticipants = $conversation->participants()->where('user_id', '!=', $user->id)->get();
            foreach ($otherParticipants as $participant) {
                broadcast(new UserTypingInConversation($participant->id, $user, $conversation));
            }
        } catch (\Exception $e) {
            Log::error('Failed to broadcast typing event', ['error' => $e->getMessage()]);
        }
        return response()->json(['status' => 'typing event sent']);
    }


	private function markMessagesAsRead(Conversation $conversation, User $user)
    {
        $unreadMessageIds = $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereDoesntHave('readReceipts', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->pluck('id');

        if ($unreadMessageIds->isNotEmpty()) {
            $readStatusData = $unreadMessageIds->map(function ($messageId) use ($user) {
                return [
                    'id' => Str::uuid(),
                    'message_id' => $messageId,
                    'user_id' => $user->id,
                    'read_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->all();
            
            MessageReadStatus::insert($readStatusData);

            // Broadcast that the messages have been read inside the active chat
            broadcast(new MessageRead($conversation->id, $user->id, $unreadMessageIds))->toOthers();

            // ADDED: Broadcast to the other participant's private channel to update their conversation list
            $otherParticipant = $conversation->participants()->where('user_id', '!=', $user->id)->first();
            if ($otherParticipant) {
                // We need to load the latest message with its receipts to send in the payload
                $conversationWithReceipts = $conversation->fresh(['latestMessage']);
                broadcast(new ConversationRead($otherParticipant->id, $conversationWithReceipts));
            }
        }
    }
}