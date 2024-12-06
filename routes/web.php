<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SentimentController;
use App\Livewire\About;
use App\Livewire\ContactUs;
use App\Livewire\SentimentAnalysis;
use App\Livewire\SentimentHistoryList;

Route::view('/', view: 'welcome');

Route::view('dashboard', 'dashboard')
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');


Route::get('about', About::class)->name('about');
Route::get('contact-us', ContactUs::class)->name('contact-us');

// Add routes for Livewire components with the updated namespace
Route::middleware(['auth'])->group(function () {
    Route::get('sentiment-analysis', SentimentAnalysis::class)->name('sentiment-analysis');
    Route::get('history', SentimentHistoryList::class)->name('history-index');
});

Route::post('/sentiment-analysis/analyze', [SentimentController::class, 'analyze'])->name('sentiment.analyze');
Route::post('/sentiment-analysis/audio-upload', [SentimentController::class, 'uploadAudio'])->name('sentiment.audio.upload');
Route::post('/sentiment/file-upload', [SentimentController::class, 'uploadDocument'])->name('sentiment.file.upload');

require __DIR__.'/auth.php';
