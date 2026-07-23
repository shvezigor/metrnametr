<article class="real-work-video">
    <button
        type="button"
        class="real-work-video__trigger"
        data-work-video
        data-video-src="{{ $video['src'] }}"
        aria-label="Відтворити: {{ $video['title'] }}"
    >
        <picture>
            <source type="image/webp" srcset="{{ $video['poster_webp'] }}">
            <img
                src="{{ $video['poster_jpg'] }}"
                width="{{ $video['width'] }}"
                height="{{ $video['height'] }}"
                alt="{{ $video['title'] }}"
                loading="lazy"
                decoding="async"
            >
        </picture>
        <span class="real-work-video__play" aria-hidden="true">▶</span>
    </button>
    <h3>{{ $video['title'] }}</h3>
</article>
