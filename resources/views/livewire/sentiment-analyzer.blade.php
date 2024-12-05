<style>
    .positive-word {
        color: green !important;
        font-weight: bold !important;
    }

    .negative-word {
        color: red !important;
        font-weight: bold !important;
    }
</style>

<div>
    <form action="{{ route('sentiment.analyze') }}" method="POST">
        @csrf
        <div>
            <h2>Enter text for sentiment analysis:</h2>
            <textarea name="text" placeholder="Type your text here" rows="4" cols="50"></textarea>
        </div>
        <button type="submit">Analyze</button>
    </form>

    @if (session('result'))
        <div class="result">
            <p><strong>Result:</strong> {{ session('result') }}</p>
        </div>
    @endif

    @if (session('highlighted_text'))
        <div class="highlighted-text">
            <p><strong>Highlighted Text:</strong></p>
            <p>{!! session('highlighted_text') !!}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="error">
            <p><strong>Error:</strong> {{ session('error') }}</p>
        </div>
    @endif
</div>
