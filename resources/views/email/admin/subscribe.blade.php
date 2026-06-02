@component('mail::message')
<h1>Вітаємо, нова підписка!</h1>
<strong>Email:</strong>  {{ $subscriber->email }}

З повагою, {{ config('app.name') }}
@endcomponent
