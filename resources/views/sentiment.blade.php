@extends('layouts.app')

@section('title', 'Sentiment Analysis')

@section('content')
<div class="sentiment-container">
    <h1>Sentiment Analysis</h1>
    <livewire:sentiment-analyzer />
</div>
@endsection
