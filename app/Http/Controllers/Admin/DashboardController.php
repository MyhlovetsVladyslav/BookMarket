<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with platform metrics.
     */
    public function index(Request $request): Response
    {
        $userStats = User::selectRaw("
            COUNT(*) as total_users,
            SUM(CASE WHEN role = 'super_admin' THEN 1 ELSE 0 END) as total_super_admins,
            SUM(CASE WHEN role = 'senior_admin' THEN 1 ELSE 0 END) as total_senior_admins
        ")->first();

        $contentStats = (object) [
            'total_books' => Book::count(),
            'total_conversations' => Conversation::count(),
            'total_messages' => Message::count(),
        ];

        $recentBooks = Book::with('user:id,name,email')
            ->latest()
            ->take(6)
            ->get();

        $recentUsers = User::latest()
            ->take(6)
            ->get(['id', 'name', 'email', 'role', 'created_at']);

        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'total_users' => (int) $userStats->total_users,
                'total_admins' => (int) $userStats->total_super_admins + (int) $userStats->total_senior_admins,
                'total_super_admins' => (int) $userStats->total_super_admins,
                'total_senior_admins' => (int) $userStats->total_senior_admins,
                'total_books' => $contentStats->total_books,
                'total_conversations' => $contentStats->total_conversations,
                'total_messages' => $contentStats->total_messages,
            ],
            'recent_books' => $recentBooks,
            'recent_users' => $recentUsers,
            'current_user_role' => $request->user()->role,
        ]);
    }
}
