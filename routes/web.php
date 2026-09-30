<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Book;

Route::get('/', function () {
    $books = Book::with('user:id,name')->latest()->paginate(24)->withQueryString();
    return Inertia::render('Welcome', [
        'books' => $books,
    ]);
})->name('home');

// Dashboard redirects to profile
Route::get('dashboard', function () {
    return redirect()->route('profile');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('books/create', [\App\Http\Controllers\BookController::class, 'create'])->name('books.create');
    Route::post('books/batch', [\App\Http\Controllers\BookController::class, 'storeBatch'])->name('books.storeBatch');
    Route::post('books', [\App\Http\Controllers\BookController::class, 'store'])->name('books.store');
    Route::get('books/{book}/edit', [\App\Http\Controllers\BookController::class, 'edit'])->name('books.edit');
    Route::put('books/{book}', [\App\Http\Controllers\BookController::class, 'update'])->name('books.update');
    Route::delete('books/{book}', [\App\Http\Controllers\BookController::class, 'destroy'])->name('books.destroy');
});

Route::get('books/{book}', [\App\Http\Controllers\BookController::class, 'show'])->name('books.show');

Route::get('/profile', function () {
    $books = \Illuminate\Support\Facades\Auth::user()->books()->latest()->get();
    return Inertia::render('user/Profile', [
        'books' => $books,
    ]);
})->middleware(['auth'])->name('profile');

// Chat routes
Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [\App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [\App\Http\Controllers\ChatController::class, 'store'])->name('chat.store');
    Route::get('/chat/{conversation}', [\App\Http\Controllers\ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}/messages', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/{conversation}/messages', [\App\Http\Controllers\ChatController::class, 'fetchMessages'])->name('chat.fetch');
});

// Admin routes (accessible to super_admin and senior_admin)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/role', [\App\Http\Controllers\Admin\UserController::class, 'updateRole'])
        ->middleware('super_admin')
        ->name('users.role');
    Route::get('/books', [\App\Http\Controllers\Admin\BookController::class, 'index'])->name('books.index');
    Route::delete('/books/{book}', [\App\Http\Controllers\Admin\BookController::class, 'destroy'])->name('books.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
