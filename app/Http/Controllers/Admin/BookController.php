<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class BookController extends Controller
{
    /**
     * Display a listing of all books for moderation.
     */
    public function index(Request $request): Response
    {
        $query = Book::with('user:id,name,email')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
            });
        }

        if ($condition = $request->input('condition')) {
            if (in_array($condition, ['Нова', 'Ідеальний стан', 'Гарний стан', 'Задовільний'], true)) {
                $query->where('condition', $condition);
            }
        }

        $books = $query->paginate(15)->withQueryString();

        return Inertia::render('admin/Books', [
            'books' => $books,
            'filters' => [
                'search' => $request->input('search', ''),
                'condition' => $request->input('condition', ''),
            ],
            'current_user_role' => $request->user()->role,
        ]);
    }

    /**
     * Remove the specified book (moderation).
     */
    public function destroy(Book $book): RedirectResponse
    {
        Gate::authorize('moderate', $book);

        if ($book->image_path) {
            Storage::disk('public')->delete($book->image_path);
        }

        $title = $book->title;
        $book->delete();

        return back()->with('success', "Оголошення '{$title}' успішно видалено модератором.");
    }
}
