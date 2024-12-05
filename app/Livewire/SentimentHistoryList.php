<?php

namespace App\Livewire;

use App\Models\SentimentHistory;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class SentimentHistoryList extends Component
{
    public $sentimentHistories;
    public $deletedHistoryId = null; // Track the deleted ID for visual feedback

    // Fetch sentiment histories when the component is mounted
    public function mount()
    {
        // Fetch the sentiment histories that belong to the logged-in user
        // These are only the records that are not soft-deleted
        $this->sentimentHistories = SentimentHistory::where('user_id', Auth::id())
                                                   ->orderByDesc('created_at')
                                                   ->get();
    }

    // Soft delete a sentiment history record (mark it as deleted, without permanently removing it)
    public function deleteHistory($id)
    {
        // Find the sentiment history by its ID and ensure the user owns it
        $history = SentimentHistory::where('user_id', Auth::id())->findOrFail($id);

        // Soft delete the record (this will set the `deleted_at` column)
        $history->delete();

        // Refresh the history list after deletion (ignores soft-deleted records)
        $this->sentimentHistories = SentimentHistory::where('user_id', Auth::id())
                                                   ->orderByDesc('created_at')
                                                   ->get();

        // Track which record was deleted for visual feedback (e.g., for highlighting)
        $this->deletedHistoryId = $id;

        // Flash a success message to notify the user
        session()->flash('message', 'Sentiment history deleted successfully!');
    }

    // Render the component view
    public function render()
    {
        // Return the Livewire component view, passing the list of sentiment histories
        return view('livewire.sentiment-history-list');
    }
}
