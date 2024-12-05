import os
import json
import sys
from textblob import TextBlob
from nltk.sentiment.vader import SentimentIntensityAnalyzer

# Read the text input from the command line argument
input_text = sys.argv[1]

# Initialize SentimentIntensityAnalyzer
sia = SentimentIntensityAnalyzer()

# Get the path to the emotion_categories.json file
# This path assumes that the file is inside public/storage/emotion_categories.json
emotion_categories_path = os.path.join(os.getcwd(), 'storage', 'emotion_categories.json')

# Check if the file exists, and raise an error if not
if not os.path.exists(emotion_categories_path):
    print(f"Error: {emotion_categories_path} does not exist.")
    sys.exit(1)

# Load emotion categories from the JSON file
with open(emotion_categories_path, 'r') as f:
    emotion_categories = json.load(f)

# Analyze sentiment using TextBlob
blob = TextBlob(input_text)

# Prepare highlighted text
highlighted_text = input_text
positive_words = []
negative_words = []

# Function to detect emotions, ignoring sentiment-related words
def detect_emotions(text):
    emotion_score = {emotion: 0 for emotion in emotion_categories}
    words = text.lower().split()

    # Skip sentiment-related words like "positive" or "negative" in the matching
    ignored_words = ["positive", "negative", "neutral"]

    for word in words:
        if word in ignored_words:
            continue  # Skip sentiment labels

        for emotion, keywords in emotion_categories.items():
            if word in keywords:
                emotion_score[emotion] += 1

    # Return the emotion with the highest score, handling neutral cases
    max_emotion = max(emotion_score, key=emotion_score.get) if max(emotion_score.values()) > 0 else "neutral"
    return max_emotion, emotion_score

# Get emotion from the text
emotion, emotion_scores = detect_emotions(input_text)

# VADER sentiment analysis
vader_scores = sia.polarity_scores(input_text)
vader_sentiment = "neutral"
if vader_scores['compound'] > 0:
    vader_sentiment = "positive"
elif vader_scores['compound'] < 0:
    vader_sentiment = "negative"

# Highlight words based on sentiment (positive or negative), avoiding clashes
for word in blob.words:
    sentiment_polarity = TextBlob(word).sentiment.polarity
    if sentiment_polarity > 0 and word not in ["positive", "negative"]:
        highlighted_text = highlighted_text.replace(word, f"<span class='positive-word'>{word}</span>")
        positive_words.append(word)
    elif sentiment_polarity < 0 and word not in ["positive", "negative"]:
        highlighted_text = highlighted_text.replace(word, f"<span class='negative-word'>{word}</span>")
        negative_words.append(word)

# VADER sentiment analysis: Highlight words and categorize as positive or negative
for word in input_text.split():
    vader_sentiment_polarity = sia.polarity_scores(word)['compound']
    if vader_sentiment_polarity > 0:
        highlighted_text = highlighted_text.replace(word, f"<span class='positive-word'>{word}</span>")
        positive_words.append(word)
    elif vader_sentiment_polarity < 0:
        highlighted_text = highlighted_text.replace(word, f"<span class='negative-word'>{word}</span>")
        negative_words.append(word)

# Prepare a response with polarity, subjectivity, highlighted text, and emotions
sentiment = blob.sentiment
response = {
    "polarity": sentiment.polarity,
    "subjectivity": sentiment.subjectivity,
    "sentiment": "positive" if sentiment.polarity > 0 else "negative" if sentiment.polarity < 0 else "neutral",
    "highlighted_text": highlighted_text,
    "positive_words": positive_words,
    "negative_words": negative_words,
    "emotion": emotion,  # This will always return the detected emotion
    "emotion_scores": emotion_scores,  # Emotion scores are also returned
    "vader_sentiment": vader_sentiment
}

# Output the response as JSON
print(json.dumps(response))
