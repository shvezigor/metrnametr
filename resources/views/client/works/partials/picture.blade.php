<picture>
    <source type="image/webp" srcset="{{ $image['webp'] }}">
    <img
        src="{{ $image['jpg'] }}"
        width="{{ $image['width'] }}"
        height="{{ $image['height'] }}"
        alt="{{ $image['alt'] }}"
        decoding="async"
        @if(!empty($priority)) fetchpriority="high" @endif
        @if(!empty($lazy)) loading="lazy" @endif
        @if(!empty($lightbox))
            data-work-image
            data-work-case-id="{{ $caseId }}"
            data-work-image-index="{{ $imageIndex }}"
        @endif
    >
</picture>
