<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Sentiment Analysis') }}
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
</style>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <form action="{{ route('sentiment.analyze') }}" method="POST" onsubmit="transformToParagraph()">
                    @csrf
                    <div>
                        <h2>Enter text for sentiment analysis:</h2>
                        <textarea id="text-input" name="text" placeholder="Type your text here" rows="4" cols="50" class="block w-full rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 focus:ring focus:ring-opacity-50"></textarea>
                    </div>
                    <button type="submit" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700 active:bg-blue-900 focus:outline-none">
                        Analyze
                    </button>
                </form>

                <button type="button" id="start-speech" class="mt-4 px-4 py-2 btn btn-warning text-white rounded-md hover:bg-green-700 active:bg-green-900 focus:outline-none">
                    Start Speech-to-Text
                </button>

                @if (session('result'))
                    <div class="result mt-6">
                        <p class="result-header">Result:</p>
                        <p class="result-text">{{ session('result') }}</p>
                    </div>
                @endif

                @if (session('highlighted_text'))
                    <div class="highlighted-text mt-4">
                        <p class="highlighted-header">Highlighted Text:</p>
                        <p class="highlighted-text">{!! session('highlighted_text') !!}</p>
                    </div>
                @endif

                @if (session('emotion_scores'))
                    <div class="emotion-scores mt-4">
                        <p class="highlighted-header">Emotion Scores:</p>
                        <ul>
                            @foreach(session('emotion_scores') as $emotion => $score)
                                <li><strong>{{ ucfirst($emotion) }}:</strong> {{ $score }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('error'))
                    <div class="error mt-4 text-red-500">
                        <p><strong>Error:</strong> {{ session('error') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('sentiment.analyze') }}" method="POST" onsubmit="transformToParagraph()">
                        @csrf
                        <div>
                            <h2>Audio sentiment analysis:</h2>
                    </form>

                    <form action="{{ route('sentiment.audio.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <h3 class="mt-6">Upload an Audio File for Transcription and Sentiment Analysis:</h3>
                        <input type="file" name="audioFile" accept="audio/*" class="mt-4">
                        <button type="submit" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700 active:bg-blue-900 focus:outline-none">
                            Upload and Analyze
                        </button>
                    </form>
                           
                    @if(session('transcribed_text'))
                        <div>
                            <strong>Transcribed Text:</strong>
                            <p>{{ session('transcribed_text') }}</p>
                        </div>
                    @endif
                        
                    <!-- Transcription Result -->
                    @if ($transcription)
                        <div class="mt-4">
                            <p><strong>Transcription:</strong> {{ $transcription }}</p>
                        </div>
                    @endif
    
    
                    @if (session('audio_result'))
                        <div class="result mt-6">
                            <p class="result-header">Result:</p>
                            <p class="result-text">{{ session('audio_result') }}</p>
                        </div>
                      @endif

                    @if (session('audio_highlighted_text'))
                        <div class="highlighted-text mt-4">
                            <p class="highlighted-header">Highlighted Text:</p>
                            <p class="highlighted-text">{!! session('audio_highlighted_text') !!}</p>
                        </div>
                    @endif

                    @if (session('audio_emotion_scores'))
                        <div class="emotion-scores mt-4">
                            <p class="highlighted-header">Emotion Scores:</p>
                            <ul>
                                @foreach(session('audio_emotion_scores') as $emotion => $score)
                                    <li><strong>{{ ucfirst($emotion) }}:</strong> {{ $score }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="error mt-4 text-red-500">
                            <p><strong>Error:</strong> {{ session('error') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>    
</div>

<script>
    function transformToParagraph() {
        // Get the textarea element
        const textArea = document.getElementById('text-input');

        // Replace all newlines with spaces
        textArea.value = textArea.value.replace(/\s+/g, ' ').trim();
    }
</script>

<script>
    // Check if the browser supports Speech Recognition
    if ('webkitSpeechRecognition' in window) {
        const recognition = new webkitSpeechRecognition();
        recognition.continuous = true;
        recognition.lang = 'en-US';
        recognition.interimResults = true;

        // Start speech recognition
        document.getElementById('start-speech').addEventListener('click', function() {
            recognition.start();
        });

        // Process the speech input
        recognition.onresult = function(event) {
            let text = '';
            for (let i = event.resultIndex; i < event.results.length; i++) {
                text += event.results[i][0].transcript;
            }

            // Display the transcribed text in the textarea
            document.getElementById('text-input').value = text;
        };

        // Handle speech recognition errors
        recognition.onerror = function(event) {
            console.error('Speech recognition error', event);
        };

        // Stop recognition when done
        recognition.onend = function() {
            console.log('Speech recognition ended');
        };
    } else {
        console.log('Speech recognition not supported in this browser.');
    }
</script>
