@component('mail::message')
<h1>Нове повідомлення!</h1>

<p><strong>Name:</strong> {{ $message->name }}</p>
<p><strong>Email:</strong> {{ $message->email }}</p>
<p><strong>Title:</strong> {{ $message->title }}</p>
<p><strong>Text:</strong> {{ $message->text }}</p>

З повагою,<br>
{{ config('app.name') }}
@endcomponent
