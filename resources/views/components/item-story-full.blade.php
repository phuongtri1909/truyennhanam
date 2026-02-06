<a href="{{ route('show.page.story', $story->slug) }}" class="completed-item d-flex text-decoration-none p-2 rounded">
    <div class="completed-item-thumb flex-shrink-0 position-relative">
        <img src="{{ $story->cover ? Storage::url($story->cover) : asset('assets/images/story_default.jpg') }}"
            alt="{{ $story->title }}" class="completed-item-img">
        <span class="completed-item-overlay">{{ Str::upper(Str::limit($story->title, 25)) }}</span>
        @if ($story->completed)
            <span class="badge-full-label">Full</span>
        @endif
    </div>
    <div class="completed-item-body flex-grow-1 min-w-0 ps-3">
        <h6 class="completed-item-title mb-2 text-dark fw-bold">
            {{ $story->title }}
        </h6>
        @if ($story->categories && $story->categories->isNotEmpty())
            <div class="completed-item-tags d-flex flex-wrap gap-1 mb-2">
                @foreach ($story->categories->take(3) as $cat)
                    <span class="completed-item-tag">{{ $cat->name }}</span>
                @endforeach
            </div>
        @endif
        <p class="completed-item-desc mb-0">
            {{ cleanDescription($story->description ?? '', 400) ?: 'Đang cập nhật...' }}
        </p>
    </div>
</a>

@once
@push('styles')
<style>
    .completed-grid-item{
        border-top: 3px solid var(--primary-color-1);
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .completed-item {
        transition: opacity 0.2s;
    }
    .completed-item:hover {
        opacity: 0.9;
    }
    .completed-item-border {
        border-bottom: 1px dotted rgba(0, 0, 0, 0.2);
    }
    .completed-item-thumb {
        width: 90px;
        height: 120px;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .completed-item-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .completed-item-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        padding: 4px 6px;
        background: linear-gradient(to bottom, rgba(0,0,0,0.85) 0%, transparent 100%);
        color: #fff;
        font-size: 0.65rem;
        font-weight: 600;
        line-height: 1.3;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .badge-full-label {
        position: absolute;
        bottom: 0;
        left: 0;
        background: var(--success-color, #28a745);
        color: #fff;
        font-size: 0.65rem;
        font-weight: 600;
        padding: 2px 6px;
        z-index: 2;
    }
    .completed-item-title {
        font-size: 0.95rem;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .completed-item-tag {
        display: inline-block;
        padding: 2px 8px;
        color: #6b7280;
        font-size: 0.7rem;
        border-radius: 4px;
        border: 1px solid var(--primary-color-1);
    }
    .completed-item-desc {
        font-size: 0.8rem;
        line-height: 1.5;
        color: #000;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 6;
        -webkit-box-orient: vertical;
    }
    body.dark-mode .completed-item-title {
        color: #e0e0e0 !important;
    }
    body.dark-mode .completed-item-tag {
        background: #404040;
        color: #9ca3af;
    }
    body.dark-mode .completed-item-desc {
        color: #9ca3af;
    }
    @media (min-width: 768px) {
        .completed-item-thumb {
            width: 100px;
            height: 140px;
        }
    }
</style>
@endpush
@endonce
