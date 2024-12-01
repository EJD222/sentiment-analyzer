<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SentimentController;
use App\Livewire\About;
use App\Livewire\ContactUs;
use App\Livewire\SentimentAnalysis;
use App\Livewire\SentimentHistoryList;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Add routes for Livewire components with the updated namespace
Route::middleware(['auth'])->group(function () {
    Route::get('about', About::class)->name('about');
    Route::get('contact-us', ContactUs::class)->name('contact-us');
    Route::get('sentiment-analysis', SentimentAnalysis::class)->name('sentiment-analysis');
    Route::get('history', SentimentHistoryList::class)->name('history-index');
});

Route::post('/sentiment-analysis/analyze', [SentimentController::class, 'analyze'])->name('sentiment.analyze');
Route::post('/sentiment-analysis/audio-upload', [SentimentController::class, 'uploadAudio'])->name('sentiment.audio.upload');

require __DIR__.'/auth.php';