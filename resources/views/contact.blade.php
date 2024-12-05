@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<div class="contact-container">
    <h1>Contact Us</h1>
    <p>Feel free to reach out to us with your questions or feedback.</p>
    <form>
        <input type="text" placeholder="Your Name" required>
        <input type="email" placeholder="Your Email" required>
        <textarea placeholder="Your Message" required></textarea>
        <button type="submit">Send</button>
    </form>
</div>
@endsection