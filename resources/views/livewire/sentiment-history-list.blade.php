<!-- resources/views/livewire/sentiment-analysis.blade.php -->
<div class="">


    <header class="site-header d-flex flex-column justify-content-center align-items-center">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-5 col-12 mb-5">
                    <h2 class="text-white  text-center">Sentiment History</h2>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Tab Content -->
    <div class="col-md-9 mx-auto pt-5"> <!-- Apply mx-auto to center the col-md-9 div -->
        <div class="tab-content" id="v-pills-tabContent">
            <!-- Text Sentiment Analysis Form -->
            <div class="tab-pane fade show active" id="v-pills-text" role="tabpanel" aria-labelledby="v-pills-text-tab">
                <section class="section-padding">
                    <div class="container">
                        <div class="col-lg-8 col-12 mx-auto mb-5"> <!-- Use mx-auto to center content -->
                            <h2 class="mb-4 text-center">Your Sentiment History</h2>
                            <p class="text-center">Review the analysis results of your past texts, including the sentiment and emotion identified.</p>
                        </div>
                    </div>
                </section>

                <div class="py-10">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="">
                            <div class="p-6 text-gray-900 dark:text-gray-100">
                                @if($sentimentHistories->isEmpty())
                                    <p class="text-gray-500">You have no sentiment analysis history yet.</p>
                                @else
                                    <table class="table-auto w-full table-striped bg-gray-50 dark:bg-gray-900 rounded-lg shadow-md">
                                        <thead>
                                            <tr class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                                <th class="px-4 py-3 text-left border-b">Text</th>
                                                <th class="px-4 py-3 text-left border-b">Sentiment</th>
                                                <th class="px-4 py-3 text-left border-b">Emotion</th>
                                                <th class="px-4 py-3 text-left border-b">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($sentimentHistories as $history)
                                                <tr class="{{ $history->id == $deletedHistoryId ? 'bg-red-100 dark:bg-red-900' : '' }} hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200">
                                                    <td class="px-4 py-3 border-b">{{ $history->text }}</td>
                                                    <td class="px-4 py-3 border-b">{{ ucfirst($history->analysis_result['sentiment'] ?? 'N/A') }}</td>
                                                    <td class="px-4 py-3 border-b">
                                                        @php
                                                            $emotion = ucfirst(strtolower($history->analysis_result['emotion'] ?? 'N/A'));
                                                            $emotionEmojis = [
                                                                'joy' => '😊',      // Joy
                                                                'anger' => '😡',    // Anger
                                                                'sadness' => '😢',   // Sadness
                                                                'surprise' => '😲',  // Surprise
                                                                'fear' => '😨',      // Fear
                                                                'disgust' => '🤢',   // Disgust
                                                                'trust' => '🤝',     // Trust
                                                                'love' => '❤️',     // Love
                                                                'N/A' => '❓'        // Not Available
                                                            ];
                                                        @endphp
                                                        {{ $emotionEmojis[strtolower($emotion)] ?? '❓' }} {{ $emotion }}
                                                    </td>
                                                    <td class="px-4 py-3 border-b">
                                                        <button wire:click.prevent="deleteHistory({{$history->id}})"
                                                                class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-700 active:bg-red-900 transition-colors duration-200 flex items-center">
                                                            <i class="bi bi-trash mr-2"></i>
                                            
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
            </div>
        </div>
    </div>
</div>
        <!-- JavaScript Files -->
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('js/jquery.sticky.js') }}"></script>
        <script src="{{ asset('js/click-scroll.js') }}"></script>
        <script src="{{ asset('js/custom.js') }}"></script>


<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

