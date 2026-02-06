@php
    $story = $story ?? null;
    $translatorHotStories = $translatorHotStories ?? collect();
    $translator = $story && $story->relationLoaded('user') ? $story->user : null;
@endphp

@if($story && $translator)
    <div class="related-stories-section">
        <a href="{{ route('translator.show', $translator) }}"
           class="translator-card d-flex flex-column align-items-center text-center p-4 rounded-4 text-decoration-none">
            <div class="translator-avatar rounded-circle overflow-hidden bg-white flex-shrink-0 mb-3">
                @if($translator->avatar ?? null)
                    <img src="{{ Storage::url($translator->avatar) }}" alt="{{ $translator->name }}" class="translator-avatar-img">
                @else
                    <div class="translator-avatar-placeholder d-flex align-items-center justify-content-center text-dark fw-bold">
                        {{ strtoupper(mb_substr($translator->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="translator-name fw-bold text-dark mb-1">{{ $translator->name }}</div>
            <div class="translator-subtitle small text-secondary">Trang cá nhân</div>
        </a>
    </div>
@endif

@if($translatorHotStories->isNotEmpty())
    <div class="related-stories-section mt-4">
        <div class="translator-same-block rounded-4 p-3">
            <h3 class="translator-same-title mb-3 text-center">Truyện cùng dịch giả</h3>
            <div class="translator-stories-list">
                @foreach($translatorHotStories as $s)
                    <a href="{{ route('show.page.story', $s->slug) }}" class="translator-story-item d-flex text-decoration-none align-items-flex-start">
                        <div class="translator-story-cover flex-shrink-0 rounded overflow-hidden">
                            <img src="{{ $s->cover ? Storage::url($s->cover) : asset('images/defaults/story_default.jpg') }}"
                                 alt="{{ $s->title }}" class="translator-story-img">
                        </div>
                        <div class="translator-story-body flex-grow-1 min-w-0 ps-3">
                            <h6 class="translator-story-title text-dark fw-bold mb-2">
                                {{ $s->title }}
                            </h6>
                            <p class="translator-story-desc mb-0 text-muted">
                                {{ cleanDescription($s->description ?? null, 200) ?: 'Đang cập nhật...' }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endif

@once
@push('styles')
<style>
/* Dịch giả card - layout dọc, nền be nhạt, bo góc */
.translator-card {
    background-color: #d6d6d68a;
    transition: background-color 0.2s ease, box-shadow 0.2s ease;
}
.translator-card:hover {
    background-color: #e5e3dd !important;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
}
.translator-avatar {
    width: 70px;
    height: 70px;
}
.translator-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.translator-avatar-placeholder {
    width: 100%;
    height: 100%;
    font-size: 1.5rem;
    background: linear-gradient(135deg, #e0e0dc, #edebe5);
}
.translator-name {
    font-size: 1rem;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}
.translator-subtitle {
    font-size: 0.875rem;
}

/* Truyện cùng dịch giả - một khung bo tròn nền sáng, list dọc */
.related-stories-section {
    margin-top: 0;
}
.translator-same-block {
    background-color: #d6d6d68a;
    border-radius: 1rem;
}
.translator-same-title {
    color: #333;
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 1rem;
}
.translator-stories-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
.translator-story-item {
    transition: opacity 0.2s ease;
}
.translator-story-item:hover {
    opacity: 0.9;
}
.translator-story-item:not(:last-child) {
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(0,0,0,0.06);
}
.translator-story-cover {
    width: 70px;
    height: 100px;
    border-radius: 6px;
}
.translator-story-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.translator-story-title {
    font-size: 0.95rem;
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.translator-story-desc {
    font-size: 0.8rem;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
    word-wrap: break-word;
}

/* Dark mode */
.dark-mode .translator-card {
    background-color: #2d2d2d !important;
}
.dark-mode .translator-card:hover {
    background-color: #383838 !important;
}
.dark-mode .translator-avatar-placeholder {
    background: linear-gradient(135deg, #404040, #505050);
    color: #e0e0e0;
}
.dark-mode .translator-name,
.dark-mode .translator-story-title { color: #e0e0e0; }
.dark-mode .translator-subtitle,
.dark-mode .translator-story-desc { color: #b0b0b0; }
.dark-mode .translator-same-title { color: #e0e0e0; }
.dark-mode .translator-same-block {
    background-color: #2d2d2d;
}
.dark-mode .translator-story-item:not(:last-child) {
    border-bottom-color: rgba(255,255,255,0.08);
}

@media (max-width: 768px) {
    .translator-story-cover { width: 50px; height: 70px; }
    .translator-story-title { font-size: 0.9rem; }
    .translator-story-desc { -webkit-line-clamp: 3; }
}
</style>
@endpush
@endonce
