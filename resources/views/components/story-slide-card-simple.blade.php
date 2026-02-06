<div class="story-slide-card-simple">
    <a href="{{ route('show.page.story', $story->slug) }}" class="story-slide-card-simple__link d-block position-relative">
        <img src="{{ $story->cover ? Storage::url($story->cover) : asset('images/defaults/story_default.jpg') }}"
             alt="{{ $story->title }}" class="story-slide-card-simple__img">
        @if ($story->is_18_plus === 1)
            @include('components.tag18plus')
        @endif
    </a>
</div>

@once
@push('styles')
<style>
/* Khi nằm trong slide (parent có aspect-ratio) dùng fill; nếu dùng riêng vẫn có padding-top */
.story-slide-card-simple {
    position: relative;
    width: 100%;
    padding-top: 140%;
    overflow: hidden;
    border-radius: 8px;
    box-sizing: border-box;
}
.story-you-may-like-slide-item .story-slide-card-simple {
    position: absolute;
    inset: 0;
    padding-top: 0;
    height: 100%;
}
.story-slide-card-simple__link {
    position: absolute;
    inset: 0;
}
.story-slide-card-simple__img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    max-width: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}
.story-slide-card-simple:hover .story-slide-card-simple__img {
    transform: scale(1.05);
}
</style>
@endpush
@endonce
