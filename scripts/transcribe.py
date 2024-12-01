import sys
import os
import logging
from pydub import AudioSegment
from vosk import Model, KaldiRecognizer
import wave

# Enable logging
logging.basicConfig(level=logging.DEBUG)

def convert_mp3_to_wav(mp3_path, wav_path):
    try:
        # Convert MP3 to WAV
        audio = AudioSegment.from_mp3(mp3_path)
        audio.export(wav_path, format="wav")
        logging.debug(f"Successfully converted {mp3_path} to {wav_path}")
    except Exception as e:
        logging.error(f"Failed to convert MP3 to WAV: {e}")
        return False
    return True

def transcribe_audio(wav_path, model_path):
    try:
        # Open WAV file
        wf = wave.open(wav_path, "rb")
        if wf.getnchannels() != 1 or wf.getsampwidth() != 2:
            logging.error("Audio file must be mono PCM WAV format")
            return None

        # Load the Vosk model
        model = Model(model_path)
        recognizer = KaldiRecognizer(model, wf.getframerate())

        # Transcribe audio
        transcription = ""
        while True:
            data = wf.readframes(4000)  # Use readframes to get audio data
            if len(data) == 0:
                break
            if recognizer.AcceptWaveform(data):
                result = recognizer.Result()
                transcription += result

        # Final result
        result = recognizer.FinalResult()
        transcription += result
        logging.debug(f"Transcription complete: {transcription}")
        return transcription

    except Exception as e:
        logging.error(f"Failed to transcribe the audio file: {e}")
        return None

def main(mp3_path, model_path):
    logging.debug(f"Audio file path: {mp3_path}")
    logging.debug(f"Model path: {model_path}")

    # Ensure the MP3 file exists
    if not os.path.exists(mp3_path):
        logging.error(f"MP3 file does not exist: {mp3_path}")
        return

    # Convert MP3 to WAV
    wav_path = mp3_path.replace(".mp3", ".wav")
    if not convert_mp3_to_wav(mp3_path, wav_path):
        return

    # Start transcription
    logging.debug(f"Start transcribing {wav_path}")
    transcription = transcribe_audio(wav_path, model_path)

    if transcription:
        print("Transcription: ", transcription)
    else:
        logging.error("Transcription failed.")

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print("Usage: python transcribe.py <audio_file> <model_path>")
    else:
        mp3_file = sys.argv[1]
        model_path = sys.argv[2]
        main(mp3_file, model_path)
