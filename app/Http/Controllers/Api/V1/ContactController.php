<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\StoreContactMessageRequest;
use App\Http\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use App\Services\ContactService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function __construct(private ContactService $contactService) {}

    public function send(StoreContactMessageRequest $request): JsonResponse
    {
        $message = $this->contactService->createMessage($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => new ContactMessageResource($message),
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $messages = $this->contactService->getMessages($request->only(['is_read', 'archived', 'per_page']));

        return response()->json([
            'success' => true,
            'message' => 'Contact messages retrieved successfully',
            'data' => ContactMessageResource::collection($messages),
        ]);
    }

    public function show(ContactMessage $contact): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Contact message retrieved successfully',
            'data' => new ContactMessageResource($contact),
        ]);
    }

    public function markAsRead(ContactMessage $contact): JsonResponse
    {
        $updated = $this->contactService->markAsRead($contact);

        return response()->json([
            'success' => true,
            'message' => 'Message marked as read',
            'data' => new ContactMessageResource($updated),
        ]);
    }

    public function destroy(ContactMessage $contact): JsonResponse
    {
        $this->contactService->deleteMessage($contact);

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully',
            'data' => null,
        ]);
    }
}
