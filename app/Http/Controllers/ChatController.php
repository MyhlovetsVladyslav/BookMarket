<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ChatController extends Controller
{
    /**
     * Show list of all conversations for the current user.
     */
    public function index()
    {
        $userId = Auth::id();

        $conversations = Conversation::where('buyer_id', $userId)
            ->orWhere('seller_id', $userId)
            ->with(['buyer', 'seller', 'book', 'latestMessage'])
            ->withCount(['messages as unread_count' => function ($q) use ($userId) {
                $q->where('user_id', '!=', $userId)->whereNull('read_at');
            }])
            ->latest('updated_at')
            ->get()
            ->map(function ($conversation) use ($userId) {
                $otherUser = $conversation->buyer_id === $userId
                    ? $conversation->seller
                    : $conversation->buyer;

                // Determine if book was deleted (book_id existed but book relation is null)
                $bookDeleted = $conversation->book_id && !$conversation->book;

                return [
                    'id' => $conversation->id,
                    'other_user' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                    ],
                    'book' => $conversation->book ? [
                        'id' => $conversation->book->id,
                        'title' => $conversation->book->title,
                        'image_path' => $conversation->book->image_path,
                    ] : null,
                    'book_title_snapshot' => $conversation->book_title_snapshot,
                    'book_deleted' => $bookDeleted,
                    'latest_message' => $conversation->latestMessage ? [
                        'body' => $conversation->latestMessage->body,
                        'created_at' => $conversation->latestMessage->created_at,
                        'is_mine' => $conversation->latestMessage->user_id === $userId,
                    ] : null,
                    'unread_count' => $conversation->unread_count,
                    'updated_at' => $conversation->updated_at,
                ];
            });

        return Inertia::render('Chat/Index', [
            'conversations' => $conversations,
        ]);
    }

    /**
     * Create or find existing conversation, then redirect to it.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'seller_id' => 'required|exists:users,id',
            'book_id' => 'nullable|exists:books,id',
        ]);

        $buyerId = Auth::id();
        $sellerId = $validated['seller_id'];

        // Can't chat with yourself
        if ($buyerId === $sellerId) {
            return redirect()->back();
        }

        // Find or create conversation
        $conversation = Conversation::where(function ($query) use ($buyerId, $sellerId) {
                $query->where('buyer_id', $buyerId)->where('seller_id', $sellerId);
            })
            ->orWhere(function ($query) use ($buyerId, $sellerId) {
                $query->where('buyer_id', $sellerId)->where('seller_id', $buyerId);
            })
            ->when($validated['book_id'] ?? null, function ($query, $bookId) {
                $query->where('book_id', $bookId);
            })
            ->first();

        if (!$conversation) {
            $bookId = $validated['book_id'] ?? null;
            $bookTitle = $bookId ? Book::find($bookId)?->title : null;

            $conversation = Conversation::create([
                'buyer_id' => $buyerId,
                'seller_id' => $sellerId,
                'book_id' => $bookId,
                'book_title_snapshot' => $bookTitle,
            ]);
        }

        return redirect()->route('chat.show', $conversation);
    }

    /**
     * Show a specific conversation with messages.
     */
    public function show(Conversation $conversation)
    {
        $userId = Auth::id();

        // Ensure user is part of this conversation
        if ($conversation->buyer_id !== $userId && $conversation->seller_id !== $userId) {
            abort(403);
        }

        // Mark unread messages as read
        $conversation->messages()
            ->where('user_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $otherUser = $conversation->buyer_id === $userId
            ? $conversation->seller
            : $conversation->buyer;

        // Determine if book was deleted
        $bookDeleted = $conversation->book_id && !$conversation->book;

        $messages = $conversation->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn ($msg) => [
                'id' => $msg->id,
                'body' => $msg->body,
                'user' => [
                    'id' => $msg->user->id,
                    'name' => $msg->user->name,
                ],
                'is_mine' => $msg->user_id === $userId,
                'created_at' => $msg->created_at,
            ]);

        return Inertia::render('Chat/Show', [
            'conversation' => [
                'id' => $conversation->id,
                'other_user' => [
                    'id' => $otherUser->id,
                    'name' => $otherUser->name,
                ],
                'book' => $conversation->book ? [
                    'id' => $conversation->book->id,
                    'title' => $conversation->book->title,
                    'image_path' => $conversation->book->image_path,
                    'price' => $conversation->book->price,
                ] : null,
                'book_title_snapshot' => $conversation->book_title_snapshot,
                'book_deleted' => $bookDeleted,
            ],
            'messages' => $messages,
        ]);
    }

    /**
     * Send a message in a conversation.
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $userId = Auth::id();

        if ($conversation->buyer_id !== $userId && $conversation->seller_id !== $userId) {
            abort(403);
        }

        $validated = $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        $conversation->messages()->create([
            'user_id' => $userId,
            'body' => $validated['body'],
        ]);

        $conversation->touch();

        return redirect()->back();
    }

    /**
     * Fetch messages for polling (JSON response).
     */
    public function fetchMessages(Request $request, Conversation $conversation)
    {
        $userId = Auth::id();

        if ($conversation->buyer_id !== $userId && $conversation->seller_id !== $userId) {
            abort(403);
        }

        // Mark unread messages as read
        $conversation->messages()
            ->where('user_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $afterId = $request->query('after_id', 0);

        $messages = $conversation->messages()
            ->where('id', '>', $afterId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn ($msg) => [
                'id' => $msg->id,
                'body' => $msg->body,
                'user' => [
                    'id' => $msg->user->id,
                    'name' => $msg->user->name,
                ],
                'is_mine' => $msg->user_id === $userId,
                'created_at' => $msg->created_at,
            ]);

        return response()->json($messages);
    }
}
