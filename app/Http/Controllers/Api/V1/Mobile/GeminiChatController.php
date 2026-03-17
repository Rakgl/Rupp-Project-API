<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\AiChatHistory;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\PetListing;
use App\Models\Product;
use App\Models\Service;
use App\Models\Store;
use App\Models\Category;
use App\Services\GeminiService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GeminiChatController extends Controller
{
    const MAX_HISTORY_MESSAGES = 10;

    /**
     * Single AI endpoint — answers questions AND can perform actions.
     * Supports conversation memory via conversation_id.
     */
    public function ask(Request $request, GeminiService $gemini): JsonResponse
    {
        $request->validate([
            'prompt' => 'required|string|max:500',
            'conversation_id' => 'nullable|uuid',
        ]);

        $user = Auth::user();
        $conversationId = $request->input('conversation_id') ?? Str::uuid()->toString();

        // Verify conversation belongs to this user (if provided)
        if ($request->filled('conversation_id')) {
            $owns = AiChatHistory::where('conversation_id', $conversationId)
                ->where('user_id', $user->id)
                ->exists();

            if (!$owns) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversation not found.',
                ], 404);
            }
        }

        // Load last N messages for context (sliding window)
        $history = AiChatHistory::where('conversation_id', $conversationId)
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(self::MAX_HISTORY_MESSAGES)
            ->get()
            ->reverse()
            ->map(fn ($msg) => [
                'role' => $msg->role,
                'message' => $msg->message,
            ])
            ->values()
            ->toArray();

        $dataContext = $this->buildUserDataContext($user);

        $tools = [
            GeminiService::appointmentToolDeclaration(),
        ];

        try {
            $reply = $gemini->askWithTools(
                $request->input('prompt'),
                $dataContext,
                $tools,
                fn (string $name, array $args) => $this->executeFunction($name, $args, $user),
                $history,
            );

            // Save user message and AI reply
            AiChatHistory::create([
                'user_id' => $user->id,
                'conversation_id' => $conversationId,
                'role' => 'user',
                'message' => $request->input('prompt'),
            ]);

            AiChatHistory::create([
                'user_id' => $user->id,
                'conversation_id' => $conversationId,
                'role' => 'model',
                'message' => $reply,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'conversation_id' => $conversationId,
                    'reply' => $reply,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Gemini API error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to process your request right now. Please try again later.',
            ], 503);
        }
    }

    /**
     * List user's AI conversations.
     */
    public function conversations(): JsonResponse
    {
        $user = Auth::user();

        $conversations = AiChatHistory::where('user_id', $user->id)
            ->selectRaw('conversation_id, MIN(message) as first_message, MAX(created_at) as last_active, COUNT(*) as message_count')
            ->groupBy('conversation_id')
            ->orderByDesc('last_active')
            ->paginate(20);

        // Get the actual first user message for each conversation as the title
        $conversationIds = $conversations->pluck('conversation_id');
        $firstMessages = AiChatHistory::whereIn('conversation_id', $conversationIds)
            ->where('role', 'user')
            ->orderBy('created_at')
            ->get()
            ->unique('conversation_id')
            ->keyBy('conversation_id');

        $conversations->getCollection()->transform(function ($conv) use ($firstMessages) {
            return [
                'conversation_id' => $conv->conversation_id,
                'title' => Str::limit($firstMessages[$conv->conversation_id]->message ?? '', 50),
                'message_count' => $conv->message_count,
                'last_active' => $conv->last_active,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $conversations,
        ]);
    }

    /**
     * Get messages for a specific conversation.
     */
    public function messages(string $conversationId): JsonResponse
    {
        $user = Auth::user();

        $messages = AiChatHistory::where('conversation_id', $conversationId)
            ->where('user_id', $user->id)
            ->orderBy('created_at')
            ->paginate(50);

        if ($messages->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation not found.',
            ], 404);
        }

        $messages->getCollection()->transform(fn ($msg) => [
            'role' => $msg->role,
            'message' => $msg->message,
            'created_at' => $msg->created_at,
        ]);

        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }

    /**
     * Delete a conversation.
     */
    public function deleteConversation(string $conversationId): JsonResponse
    {
        $user = Auth::user();

        $deleted = AiChatHistory::where('conversation_id', $conversationId)
            ->where('user_id', $user->id)
            ->delete();

        if ($deleted === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Conversation deleted.',
        ]);
    }

    /**
     * Execute a function call from Gemini — scoped to the authenticated user.
     */
    protected function executeFunction(string $name, array $args, $user): array
    {
        return match ($name) {
            'create_appointment' => $this->createAppointment($args, $user),
            default => ['error' => 'Unknown function.'],
        };
    }

    /**
     * Create an appointment — validates ownership and availability.
     */
    protected function createAppointment(array $args, $user): array
    {
        $pet = Pet::where('user_id', $user->id)
            ->where('name', 'ILIKE', $args['pet_name'] ?? '')
            ->first();

        if (!$pet) {
            return ['error' => "Could not find a pet named \"{$args['pet_name']}\" in your account."];
        }

        $locale = app()->getLocale();
        $service = Service::where('status', 'ACTIVE')
            ->get()
            ->first(function ($s) use ($args, $locale) {
                $name = $s->name[$locale] ?? $s->name['en'] ?? '';
                return str_contains(strtolower($name), strtolower($args['service_name'] ?? ''));
            });

        if (!$service) {
            return ['error' => "Could not find a service matching \"{$args['service_name']}\"."];
        }

        try {
            $startTime = Carbon::parse($args['start_time'], 'Asia/Phnom_Penh');
        } catch (\Exception $e) {
            return ['error' => "Invalid date/time format: \"{$args['start_time']}\". Use YYYY-MM-DD HH:MM."];
        }

        if ($startTime->isPast()) {
            return ['error' => 'The appointment time must be in the future.'];
        }

        $endTime = $startTime->copy()->addMinutes($service->duration_minutes);

        $overlapping = Appointment::where('pet_id', $pet->id)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
            })
            ->where('status', '!=', 'CANCELLED')
            ->exists();

        if ($overlapping) {
            return ['error' => 'There is already an appointment for this pet during that time.'];
        }

        $storeId = Store::first()?->id;
        if (!$storeId) {
            return ['error' => 'Service is currently unavailable. No store configured.'];
        }

        $appointment = Appointment::create([
            'user_id' => $user->id,
            'store_id' => $storeId,
            'pet_id' => $pet->id,
            'service_id' => $service->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'special_requests' => $args['special_requests'] ?? null,
            'status' => 'PENDING',
        ]);

        $serviceName = $service->name[$locale] ?? $service->name['en'] ?? 'Unknown';

        return [
            'success' => true,
            'message' => 'Appointment booked successfully.',
            'appointment' => [
                'pet' => $pet->name,
                'service' => $serviceName,
                'start_time' => $startTime->format('Y-m-d H:i'),
                'end_time' => $endTime->format('Y-m-d H:i'),
                'status' => 'PENDING',
            ],
        ];
    }

    /**
     * Build data context scoped to the authenticated user's permissions.
     */
    protected function buildUserDataContext($user): string
    {
        $locale = app()->getLocale();
        $sections = [];

        $pets = Pet::where('user_id', $user->id)
            ->with('category')
            ->get()
            ->map(fn ($pet) => [
                'name' => $pet->name,
                'species' => $pet->species,
                'breed' => $pet->breed,
                'weight' => $pet->weight,
                'date_of_birth' => $pet->date_of_birth?->format('Y-m-d'),
                'category' => $pet->category?->name[$locale] ?? $pet->category?->name['en'] ?? null,
            ]);

        if ($pets->isNotEmpty()) {
            $sections[] = "USER'S PETS:\n" . $pets->toJson(JSON_PRETTY_PRINT);
        }

        $products = Product::where('status', 'ACTIVE')
            ->with(['category', 'storeInventories'])
            ->limit(50)
            ->get()
            ->map(fn ($product) => [
                'name' => $product->name[$locale] ?? $product->name['en'] ?? null,
                'description' => $product->description[$locale] ?? $product->description['en'] ?? null,
                'price' => $product->price,
                'category' => $product->category?->name[$locale] ?? $product->category?->name['en'] ?? null,
                'in_stock' => $product->storeInventories->sum('quantity') > 0,
            ]);

        if ($products->isNotEmpty()) {
            $sections[] = "PRODUCTS (Accessories & Supplies):\n" . $products->toJson(JSON_PRETTY_PRINT);
        }

        $listings = PetListing::where('status', 'ACTIVE')
            ->with('pet.category')
            ->limit(50)
            ->get()
            ->map(fn ($listing) => [
                'listing_type' => $listing->listing_type,
                'price' => $listing->price,
                'description' => $listing->description,
                'pet_name' => $listing->pet?->name,
                'pet_species' => $listing->pet?->species,
                'pet_breed' => $listing->pet?->breed,
                'category' => $listing->pet?->category?->name[$locale] ?? $listing->pet?->category?->name['en'] ?? null,
            ]);

        if ($listings->isNotEmpty()) {
            $sections[] = "PET LISTINGS (For Sale / Adoption):\n" . $listings->toJson(JSON_PRETTY_PRINT);
        }

        $services = Service::where('status', 'ACTIVE')
            ->limit(30)
            ->get()
            ->map(fn ($service) => [
                'name' => $service->name[$locale] ?? $service->name['en'] ?? null,
                'description' => $service->description[$locale] ?? $service->description['en'] ?? null,
                'price' => $service->price,
                'duration_minutes' => $service->duration_minutes,
            ]);

        if ($services->isNotEmpty()) {
            $sections[] = "SERVICES (Grooming, Vet, etc.):\n" . $services->toJson(JSON_PRETTY_PRINT);
        }

        $categories = Category::where('status', 'ACTIVE')
            ->get()
            ->map(fn ($cat) => [
                'name' => $cat->name[$locale] ?? $cat->name['en'] ?? null,
                'type' => $cat->type,
            ]);

        if ($categories->isNotEmpty()) {
            $sections[] = "CATEGORIES:\n" . $categories->toJson(JSON_PRETTY_PRINT);
        }

        return implode("\n\n", $sections) ?: 'No data available.';
    }
}
