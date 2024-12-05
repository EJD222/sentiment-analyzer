<!-- resources/views/livewire/sentiment-analysis.blade.php -->
<div class="">

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


    <header class="site-header d-flex flex-column justify-content-center align-items-center">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-5 col-12 mb-5">
                    <h2 class="text-white  text-center">Sentiment Analysis</h2>
                </div>
            </div>
        </div>
    </header>



<!-- Sentiment Analysis Blade Template with Vertical Tabs -->

<!-- Sentiment Analysis Blade Template with Vertical Tabs -->

<!-- Container for Vertical Tabs -->
<div class="container mt-5">
    <div class="row">
        <!-- Vertical Tabs -->
        <div class="col-md-3">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <a class="nav-link active" id="v-pills-text-tab" data-bs-toggle="pill" href="#v-pills-text" role="tab" aria-controls="v-pills-text" aria-selected="true">Text Sentiment Analysis</a>
                <a class="nav-link" id="v-pills-document-tab" data-bs-toggle="pill" href="#v-pills-document" role="tab" aria-controls="v-pills-document" aria-selected="false">Document Sentiment Analysis</a>
                <a class="nav-link" id="v-pills-audio-tab" data-bs-toggle="pill" href="#v-pills-audio" role="tab" aria-controls="v-pills-audio" aria-selected="false">Audio Sentiment Analysis</a>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="col-md-9">
            <div class="tab-content" id="v-pills-tabContent">

                <!-- Text Sentiment Analysis Form -->
                <div class="tab-pane fade show active" id="v-pills-text" role="tabpanel" aria-labelledby="v-pills-text-tab">
                    <section class="section-padding">
                        <div class="container">
                            <div class="col-lg-8 col-12 mx-auto mb-5">
                                <h2 class="mb-4 text-center">Text and Speech-to-Text Sentiment Analysis</h2>
                                <p class="text-center">Simply type or paste some text into the box below and click the "Analyze Text!" button.</p>                        
                            </div>
                        </div>
                    </section>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100" style="max-height: 500px; overflow-y: auto;">
                            <form action="{{ route('sentiment.analyze') }}" method="POST" onsubmit="transformToParagraph()">
                                @csrf
                                <!-- Text Area -->
                                <textarea id="text-input" name="text" placeholder="Type your text here" rows="6" class="block w-full rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 focus:ring focus:ring-opacity-50 mb-4"></textarea>
                                
                                <!-- Analyze Text Button (Primary Action) -->
                                <button type="submit" class="mt-4 px-6 py-3 bg-blue-700 text-white rounded-md hover:bg-blue-800 active:bg-blue-900 focus:outline-none">
                                    Analyze Text!
                                </button>
                            </form>

                            <!-- Speech-to-Text Button (Secondary Action) -->
                            <button type="button" id="start-speech" class="mt-4 px-6 py-3 bg-green-500 text-white rounded-md hover:bg-green-700 active:bg-green-900 focus:outline-none">
                                Start Speech-to-Text
                            </button>

                            @if (session('result'))
                                <div class="result mt-6 p-4 bg-blue-100 rounded-md">
                                    <p class="font-bold">Result:</p>
                                    <p>{{ session(key: 'result') }}</p>
                                </div>
                            @endif

                            @if (session('highlighted_text'))
                                <div class="highlighted-text mt-6 p-4 bg-blue-100 rounded-md">
                                    <p class="highlighted-header">Highlighted Text:</p>
                                    <p class="highlighted-text">{!! session('highlighted_text') !!}</p>
                                </div>
                            @endif

                            @if (session('emotion_scores'))
                                <div class="emotion-scores mt-6 p-4 bg-blue-100 rounded-md">
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

                <!-- Document Sentiment Analysis Form -->
                <div class="tab-pane fade" id="v-pills-document" role="tabpanel" aria-labelledby="v-pills-document-tab">
                    <section class="section-padding">
                        <div class="container">
                            <div class="col-lg-8 col-12 mx-auto mb-5">
                                <h2 class="mb-4 text-center">Document Sentiment Analysis</h2>
                                <p class="text-center">Upload a document file (.docx or .pdf) to begin the analysis. Ensure the file is plain text for best results.</p>
                            </div>
                        </div>
                    </section>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100" style="max-height: 500px; overflow-y: auto;">
                        <form action="{{ route('sentiment.file.upload') }}" method="POST" enctype="multipart/form-data" class="mx-auto" style="max-width: 500px;">
    @csrf
    <div class="d-flex flex-column align-items-center">
        <!-- File Input -->
        <input type="file" name="document" accept=".docx,.pdf" class="mt-4 form-control w-100">
        
        <!-- Upload and Analyze Button -->
        <button type="submit" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700 active:bg-blue-900 focus:outline-none">
            Upload and Analyze
        </button>
    </div>
</form>
                    <!-- Display Document Analysis Results -->
                    @if (session('file_result'))
                        <div class="result mt-6">
                            <p class="result-header">Document Analysis Result:</p>
                            <p class="result-text">{{ session('file_result') }}</p>
                        </div>
                    @endif
    
                    @if (session('file_highlighted_text'))
                        <div class="highlighted-text mt-4">
                            <p class="highlighted-header">Highlighted Text:</p>
                            <p class="highlighted-text">{!! session('file_highlighted_text') !!}</p>
                        </div>
                    @endif
    
                    @if (session('file_emotion_scores'))
                        <div class="emotion-scores mt-4">
                            <p class="highlighted-header">Emotion Scores:</p>
                            <ul>
                                @foreach(session('file_emotion_scores') as $emotion => $score)
                                    <li><strong>{{ ucfirst($emotion) }}:</strong> {{ $score }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
    
                    @if (session('error'))
                        <div class="error mt-4 text-red-500">
                            <p><strong>Error:</strong> {{ session('error') }}</p>
                        </div>
                    @endif                        </div>
                    </div>
                </div>

                <!-- Audio Sentiment Analysis Form -->
                <div class="tab-pane fade" id="v-pills-audio" role="tabpanel" aria-labelledby="v-pills-audio-tab">
                    <section class=" section-padding">
                        <div class="container">
                            <div class="col-lg-8 col-12 mx-auto mb-5">
                                <h2 class="mb-4 text-center">Audio Sentiment Analysis</h2>
                                <p class="text-center">Upload audio recordings and have them transcribed and analyzed for sentiment. Perfect for understanding the emotion behind voice recordings</p>                        
                            </div>
                        </div>
                    </section>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100" style="max-height: 500px; overflow-y: auto;">
                        <form action="{{ route('sentiment.audio.upload') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="d-flex flex-column align-items-center">
        <!-- File Input -->
        <input type="file" name="audioFile" accept="audio/*" class="mt-4 form-control w-50">
        
        <!-- Upload and Analyze Button -->
        <button type="submit" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700 active:bg-blue-900 focus:outline-none">

            Upload and Analyze
        </button>
    </div>
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
    </div>
</div>
        <!-- JavaScript Files -->
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('js/jquery.sticky.js') }}"></script>
        <script src="{{ asset('js/click-scroll.js') }}"></script>
        <script src="{{ asset('js/custom.js') }}"></script>


<!-- Bootstrap 5 JS (Ensure Bootstrap JS and CSS are included in your project) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</div> <!-- Closing the single root div -->

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
