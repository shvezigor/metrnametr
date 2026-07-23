@if(!empty($landing['internal_links']))
    <nav class="commercial-related" aria-labelledby="commercial-related-title">
        <h2 id="commercial-related-title">Суміжні сторінки</h2>
        <ul>
            @foreach($landing['internal_links'] as $link)
                <li><a href="{{ $link['path'] }}">{{ $link['title'] }}</a></li>
            @endforeach
        </ul>
    </nav>
@endif
