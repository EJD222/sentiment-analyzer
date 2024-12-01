<?php

namespace App\Http\Controllers;

use App\Models\SentimentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;

class SentimentController extends Controller
{
    public function analyze(Request $request)
    {
        // Validate input text to ensure it meets the requirements
        $request->validate([
            'text' => 'required|string|max:100000',
        ]);

        // Get the input text from the request
        $text = $request->input('text');

        // Define the path to the sentiment analysis Python script
        $scriptPath = base_path('scripts/sentiment_analysis.py');

        // Ensure the script path is correct and it exists
        if (!file_exists($scriptPath)) {
            Log::error("Python script not found at: $scriptPath");
            return redirect()->route('sentiment-analysis')->with('error', 'Sentiment analysis script not found.');
        }

        // Escape the text to prevent shell injection, and build the shell command
        $command = sprintf(
            'python %s %s',
            escapeshellarg($scriptPath),
            escapeshellarg($text)
        );

        // Log the command for debugging purposes
        Log::info('Running Python sentiment analysis: ' . $command);

        // Execute the command and capture both stdout and stderr
        $output = shell_exec($command . ' 2>&1');
        Log::info('Python script output: ' . $output);

        // Decode the JSON response from the Python script
        $response = json_decode($output, true);

        // Check if the response is valid and contains the necessary sentiment data
        if (isset($response['sentiment'])) {
            // Prepare the sentiment result
            $formattedResult = sprintf(
                "Sentiment: %s\nPolarity: %s\nSubjectivity: %s\nEmotion: %s\nVader Sentiment: %s",
                ucfirst($response['sentiment']),
                $response['polarity'],
                $response['subjectivity'],
                ucfirst($response['emotion']),
                ucfirst($response['vader_sentiment'])
            );

            // Save the sentiment history to the database
            SentimentHistory::create([
                'user_id' => Auth::id(), // Assuming user is authenticated
                'text' => $text,
                'analysis_result' => [  // Save the analysis result as an array
                    'sentiment' => $response['sentiment'],
                    'polarity' => $response['polarity'],
                    'subjectivity' => $response['subjectivity'],
                    'emotion' => $response['emotion'],
                    'vader_sentiment' => $response['vader_sentiment'],
                ],
                'emotion_scores' => $response['emotion_scores'], // Store emotion scores
                'highlighted_text' => $response['highlighted_text'], // Store highlighted text
            ]);

            // Return the result to the view
            return redirect()->route('sentiment-analysis')->with([
                'result' => $formattedResult,
                'highlighted_text' => $response['highlighted_text'],
                'emotion_scores' => $response['emotion_scores']
            ]);
        } else {
            // If there was an issue with the Python output, log it and show an error message
            Log::error('Error analyzing sentiment. Command Output: ' . $output);
            return redirect()->route('sentiment-analysis')->with('error', 'Error analyzing sentiment.');
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
        return redirect()->route('sentiment-analysis')->with([
            'audio_result' => $sentimentData['formatted_result'],
            'audio_highlighted_text' => $sentimentData['highlighted_text'],
            'audio_emotion_scores' => $sentimentData['emotion_scores'],
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

    
}