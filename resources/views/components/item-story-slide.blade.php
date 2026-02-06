{{-- Card dùng cho slide Truyện miễn phí (layout cũ của Đã hoàn) --}}
<div class="story-item-full d-flex align-items-start">
    <div class="story-image-container flex-shrink-0 me-3">
        <a href="{{ route('show.page.story', $story->slug) }}" class="d-block position-relative">
            <img src="{{ $story->cover ? Storage::url($story->cover) : asset('assets/images/story_default.jpg') }}"
                alt="{{ $story->title }}" class="img-fluid rounded-3 image-story-full-item">
            @if ($story->is_18_plus === 1)
                @include('components.tag18plus')
            @endif
        </a>
        @if ($story->completed)
            <span class="badge-full-full">Full</span>
        @endif
    </div>
    <div class="story-content-container flex-grow-1 min-w-0">
        <div class="story-header">
            <h5 class="story-title mb-1 text-sm fw-semibold lh-base">
                <a href="{{ route('show.page.story', $story->slug) }}" class="text-decoration-none color-3">
                    {{ Str::limit($story->title, 25, '...') }}
                </a>
            </h5>
            @if ($story->latestChapter)
                <p class="text-ssm text-gray-600 mb-2">{{ $story->latestChapter->created_at->format('d/m/Y') }}</p>
            @endif
        </div>
        <div class="story-description">
            {{ cleanDescription($story->description, 120) }}
        </div>
    </div>
</div>
