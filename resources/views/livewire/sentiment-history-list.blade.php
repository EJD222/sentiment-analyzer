<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Sentiment History') }}
    </h2>
</x-slot>

<style>
    .positive-word {
        color: green !important;
        font-weight: bold !important;
    }

    .negative-word {
        color: red !important;
        font-weight: bold !important;
    }

    .result-header {
        font-size: 1.25rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .result-text {
        margin-bottom: 1rem;
    }

    .highlighted-header {
        font-size: 1.25rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .highlighted-text {
        line-height: 1.5;
    }

    .emotion-scores {
        margin-top: 1rem;
    }

    .emotion-scores p {
        font-weight: bold;
    }

    /* Add a class for deleted history */
    .deleted-history {
        background-color: #f8d7da !important;
    }
</style>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">

                <!-- Display a message if there are no sentiment histories -->
                @if($sentimentHistories->isEmpty())
                    <p class="text-gray-500">You have no sentiment analysis history yet.</p>
                @else
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-6">
                        Your Sentiment History
                    </h2>

                    <!-- Display a list of sentiment histories -->
                    <table class="table-auto w-full">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 border-b">Text</th>
                                <th class="px-4 py-2 border-b">Sentiment</th>
                                <th class="px-4 py-2 border-b">Emotion</th>
                                <th class="px-4 py-2 border-b">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sentimentHistories as $history)
                                <tr class="{{ $history->id == $deletedHistoryId ? 'deleted-history' : '' }}">
                                    <td class="px-4 py-2 border-b">{{ $history->text }}</td>
                                    <td class="px-4 py-2 border-b">
                                        @if(isset($history->analysis_result['sentiment']))
                                            {{ ucfirst($history->analysis_result['sentiment']) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 border-b">
                                        @if(isset($history->analysis_result['emotion']))
                                            {{ ucfirst($history->analysis_result['emotion']) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 border-b">
                                        <button wire:click="deleteHistory({{ $history->id }})"
                                                class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-700 active:bg-red-900">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if (session('message'))
                        <div class="mt-4 text-green-500">
                            <p><strong>{{ session('message') }}</strong></p>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
