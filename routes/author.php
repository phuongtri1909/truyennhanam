<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Author\AuthorController;
use App\Http\Controllers\Author\StoryController;
use App\Http\Controllers\Author\ChapterController;
use App\Http\Controllers\Author\AuthorApplicationController;

Route::group(['as' => 'author.', 'middleware' => 'auth'], function () {
    Route::middleware('role:author')->group(function () {
        Route::get('/', [AuthorController::class, 'index'])->name('index');
        Route::resource('stories', StoryController::class);
        Route::post('stories/{story}/submit', [StoryController::class, 'submitForReview'])->name('stories.submit');
        Route::delete('stories/{story}/edit-requests/{editRequest}', [StoryController::class, 'withdrawEditRequest'])->name('stories.edit-requests.withdraw');
        Route::get('stories/{story}/chapters/bulk-create', [ChapterController::class, 'bulkCreate'])->name('stories.chapters.bulk-create');
        Route::post('stories/{story}/chapters/bulk-store', [ChapterController::class, 'bulkStore'])->name('stories.chapters.bulk-store');
        Route::get('stories/{story}/chapters/bulk-edit-price', [ChapterController::class, 'bulkEditPrice'])->name('stories.chapters.bulk-edit-price');
        Route::post('stories/{story}/chapters/bulk-update-price', [ChapterController::class, 'bulkUpdatePrice'])->name('stories.chapters.bulk-update-price');
        Route::post('stories/{story}/chapters/check-existing', [ChapterController::class, 'checkExisting'])->name('stories.chapters.check-existing');
        Route::get('get-server-time', fn() => response()->json(['time' => now('Asia/Ho_Chi_Minh')->format('Y-m-d\TH:i')]))->name('get-server-time');
        Route::resource('stories.chapters', ChapterController::class);
    });


    Route::get('/author-application', [AuthorApplicationController::class, 'showApplicationForm'])->name('application');
    Route::post('/author-application', [AuthorApplicationController::class, 'submitApplication'])->name('submit');
});
