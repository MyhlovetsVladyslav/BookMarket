<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function create()
    {
        return Inertia::render('books/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'condition' => 'required|in:Нова,Ідеальний стан,Гарний стан,Задовільний',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('books', 'public');
        }
        unset($validated['image']);

        $book = $request->user()->books()->create($validated);

        return redirect()->route('books.show', $book);
    }

    public function storeBatch(Request $request)
    {
        $validated = $request->validate([
            'books' => 'required|array|min:1',
            'books.*.title' => 'required|string|max:255',
            'books.*.author' => 'required|string|max:255',
            'books.*.price' => 'required|numeric|min:0',
            'books.*.condition' => 'required|in:Нова,Ідеальний стан,Гарний стан,Задовільний',
            'books.*.image' => 'required|image|max:5120',
        ]);

        $booksToInsert = [];
        $now = now();
        $userId = $request->user()->id;

        foreach ($request->file('books') as $index => $bookFiles) {
            $bookData = $request->input("books.{$index}");
            
            $imagePath = $bookFiles['image']->store('books', 'public');

            $booksToInsert[] = [
                'user_id' => $userId,
                'title' => $bookData['title'],
                'author' => $bookData['author'],
                'price' => $bookData['price'],
                'condition' => $bookData['condition'],
                'image_path' => $imagePath,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        Book::insert($booksToInsert);

        return redirect()->route('profile')->with('success', 'Книги успішно опубліковані!');
    }

    public function show(Book $book)
    {
        $book->load('user');
        return Inertia::render('books/Show', [
            'book' => $book,
        ]);
    }

    public function edit(Book $book)
    {
        Gate::authorize('update', $book);

        return Inertia::render('books/Edit', [
            'book' => $book,
        ]);
    }

    public function update(Request $request, Book $book)
    {
        Gate::authorize('update', $book);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'condition' => 'required|in:Нова,Ідеальний стан,Гарний стан,Задовільний',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('image')) {
            if ($book->image_path) {
                Storage::disk('public')->delete($book->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('books', 'public');
        }
        unset($validated['image']);

        $book->update($validated);

        return redirect()->route('books.show', $book);
    }

    public function destroy(Book $book)
    {
        Gate::authorize('delete', $book);

        if ($book->image_path) {
            Storage::disk('public')->delete($book->image_path);
        }

        $title = $book->title;
        $book->delete();

        return redirect()->back()->with('success', "Оголошення '{$title}' успішно видалено.");
    }
}
