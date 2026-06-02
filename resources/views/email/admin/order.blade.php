@component('mail::message')
<h1>Нове замовлення!</h1>

<p><strong>Name:</strong> {{ $order->name }}</p>
<p><strong>Phone:</strong> {{ $order->phone }}</p>
@if($order->product_id)
<p><strong>Product ID:</strong> {{ $order->product_id }}</p>
@endif

З повагою,<br>
{{ config('app.name') }}
@endcomponent
