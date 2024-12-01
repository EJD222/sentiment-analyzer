<?php

namespace App\Livewire;

use App\Models\SentimentHistory;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;

class SentimentAnalysis extends Component
{
    use WithFileUploads;
    public $text;  // Text input for sentiment analysis
    public $audioFile; // Audio file input
    public $transcription; // Transcription result
    public $result; // Sentiment analysis result
    public $highlightedText; // Highlighted words in the text
    public $emotionScores; // Emotion scores

    // Function to analyze sentiment
    public function analyze()
    {
        // Validate text is provided
        if (empty($this->text)) {
            $this->result = 'Please enter some text for analysis.';
            return;
        }

        // Path to the sentiment analysis Python script
        $scriptPath = base_path('scripts/sentiment_analysis.py');

        // Build the command to run the Python script
        $command = sprintf(
            'python %s %s',  // Passing the path to the script and the text as argument
            escapeshellarg($scriptPath),
            escapeshellarg($this->text)
        );

        // Log the command for debugging purposes
        Log::info('Running command: ' . $command);

        // Execute the Python script and capture both stdout and stderr
        $output = shell_exec($command . ' 2>&1');
        Log::info('Python script output: ' . $output);

        // Decode the JSON response from the Python script
        $response = json_decode($output, true);

        // Check if the response contains necessary sentiment data
        if (isset($response['sentiment'])) {
            // Set the highlighted text and result message
            $this->highlightedText = $response['highlighted_text'];
            $this->result = sprintf(
                "Sentiment: %s\nPolarity: %s\nSubjectivity: %s\nEmotion: %s\nVader Sentiment: %s",
                ucfirst($response['sentiment']),
                $response['polarity'],
                $response['subjectivity'],
                ucfirst($response['emotion']),
                ucfirst($response['vader_sentiment'])
            );

            // Set the emotion scores
            $this->emotionScores = $response['emotion_scores'];

            // Save to database after analysis
            SentimentHistory::create([
                'user_id' => Auth::id(), // Assuming user is authenticated
                'text' => $this->text,
                'analysis_result' => [  // Save the analysis result as an array
                    'sentiment' => $response['sentiment'],
                    'polarity' => $response['polarity'],
                    'subjectivity' => $response['subjectivity'],
                    'emotion' => $response['emotion'],
                    'vader_sentiment' => $response['vader_sentiment'],
                ],
                'emotion_scores' => $this->emotionScores, // Store emotion scores
                'highlighted_text' => $this->highlightedText, // Store highlighted text
            ]);
        } else {
            // In case of error, set a failure message
            $this->result = 'Error analyzing sentiment.';
        }
    }

    public function uploadAudio(Request $request)
    {
        Log::info('Audio upload process started.');

        // Validate the audio file
        try {
            $request->validate([
                'audioFile' => 'required|mimes:mp3,wav,ogg|max:10240',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed: ' . $e->getMessage());
            return redirect()->route('sentiment-analysis')->with('error', 'Invalid file uploaded. Please upload a valid audio file.');
        }

        if ($request->hasFile('audioFile')) {
            Log::info('Audio file detected in the request.');

            try {
                // Define the directory and ensure it exists
                $directory = storage_path('app/audio_files');
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                    Log::info('Created audio_files directory at: ' . $directory);
                }

                // Define the file name and full path
                $fileName = uniqid() . '.' . $request->file('audioFile')->getClientOriginalExtension();
                $fullPath = $directory . DIRECTORY_SEPARATOR . $fileName;

                // Move the file to the directory
                $request->file('audioFile')->move($directory, $fileName);
                Log::info('Audio file moved to: ' . $fullPath);

                // Verify the file exists
                if (!file_exists($fullPath)) {
                    Log::error('File missing after move: ' . $fullPath);
                    return redirect()->route('sentiment-analysis')->with('error', 'File upload failed.');
                }

                // Proceed to transcription
                $transcription = $this->transcribeAudio($fullPath);

                if ($transcription) {
                    Log::info('Transcription completed successfully.');
                    // Directly call sentiment analysis with transcription
                    return $this->analyzeTranscription($transcription);
                } else {
                    Log::error('Transcription process failed.');
                    return redirect()->route('sentiment-analysis')->with('error', 'Failed to transcribe the audio file.');
                }
            } catch (\Exception $e) {
                Log::error('Error during audio upload process: ' . $e->getMessage());
                return redirect()->route('sentiment-analysis')->with('error', 'An unexpected error occurred during the upload process.');
            }
        } else {
            Log::error('No audio file detected in the request.');
            return redirect()->route('sentiment-analysis')->with('error', 'No audio file uploaded.');
        }
    }


    private function transcribeAudio($filePath)
    {
        Log::info('Starting transcription process for file: ' . $filePath);

        try {
            // Path to the Vosk model
            $modelPath = storage_path('app/model/vosk_model/vosk-model-small-en-us-0.15');
            Log::info('Using Vosk model path: ' . $modelPath);

            if (!is_dir($modelPath)) {
                Log::error('Vosk model path does not exist or is not a directory: ' . $modelPath);
                return null;
            }

            // Build the transcription command
            $command = sprintf(
                'python %s %s %s',
                escapeshellarg(base_path('scripts/transcribe.py')),
                escapeshellarg($filePath),
                escapeshellarg($modelPath)
            );

            // Run the command and capture the output
            $output = shell_exec($command);
            Log::info('Raw Transcription Output: ' . $output); // Log the raw output

            // Remove "Transcription: " from the start of the output
            $cleanedOutput = preg_replace('/^Transcription:\s*/', '', $output); // Remove any "Transcription: " text

            // Decode the transcription result
            $response = json_decode($cleanedOutput, true);

            // Check for JSON errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON Decoding Error: ' . json_last_error_msg());
            } else {
                Log::info('Decoded Transcription Response: ' . json_encode($response));
            }

            // If transcription is successful, return the text
            if (isset($response['text']) && !empty(trim($response['text']))) {
                Log::info('Transcribed Text: ' . $response['text']);
                return $response['text']; // Successfully transcribed
            } else {
                Log::error('No valid text found in transcription response');
                return null; // Failed to transcribe
            }
        } catch (\Exception $e) {
            Log::error('Error during transcription: ' . $e->getMessage());
            return null;
        }
    }

    public function analyzeTranscription($transcription)
{
    Log::info('Starting sentiment analysis for transcription: ' . $transcription);

    // Perform sentiment analysis on the transcription text
    $sentimentData = $this->performSentimentAnalysis($transcription);

    // Check if sentiment analysis was successful
    if ($sentimentData) {
        Log::info('Sentiment analysis successful.');
        return redirect()->route('sentiment-analysis')->with([
            'result' => $sentimentData['formatted_result'],
            'highlighted_text' => $sentimentData['highlighted_text'],
            'emotion_scores' => $sentimentData['emotion_scores'],
            'transcribed_text' => $transcription,
        ]);
    } else {
        Log::error('Sentiment analysis failed.');
        return redirect()->route('sentiment-analysis')->with('error', 'Failed to analyze sentiment.');
    }
}

private function performSentimentAnalysis($text)
{
    $scriptPath = base_path('scripts/sentiment_analysis.py');
    $command = sprintf(
        'python %s %s',
        escapeshellarg($scriptPath),
        escapeshellarg($text)
    );

    $output = shell_exec($command . ' 2>&1');
    Log::info('Python script output: ' . $output);

    $response = json_decode($output, true);

    if (isset($response['sentiment'])) {
        return [
            'formatted_result' => sprintf(
                "Sentiment: %s\nPolarity: %s\nSubjectivity: %s\nEmotion: %s\nVader Sentiment: %s",
                ucfirst($response['sentiment']),
                $response['polarity'],
                $response['subjectivity'],
                ucfirst($response['emotion']),
                ucfirst($response['vader_sentiment'])
            ),
            'highlighted_text' => $response['highlighted_text'],
            'emotion_scores' => $response['emotion_scores'],
        ];
    }

    return null;
}


    // Render the Livewire component view
    public function render()
    {
        return view('livewire.sentiment-analysis', [
            'highlightedText' => $this->highlightedText,
            'emotionScores' => $this->emotionScores,
            'transcription' => $this->transcription,
        ]); // Ensure layout is used if needed
    }
}

