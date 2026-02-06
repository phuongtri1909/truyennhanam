<section class="truyen-moi-cap-nhat-section">
    <div class="d-flex align-items-center justify-content-between mb-3 px-3">
        <h2 class="m-0 text-dark fw-bold title-dark fs-5 font-svn-apple color-3">Truyện mới cập nhật</h2>
        <a href="{{ route('story.new.chapter') }}" class="text-decoration-none fw-semibold fs-7 color-2">Xem thêm &raquo;</a>
    </div>

    <div class="border border-color-4 bg-white">
        <ul class="tmnc-simple-list list-unstyled mb-0 px-2">
            @foreach ($latestUpdatedStories as $index => $story)
                <li class="tmnc-list-item py-2 {{ $index >= 10 ? 'd-none d-md-block' : '' }} {{ $index < $latestUpdatedStories->count() - 1 ? 'tmnc-list-item-border' : '' }}">
                    <a href="{{ route('show.page.story', $story->slug) }}" class="text-decoration-none text-dark fw-semibold tmnc-list-title">
                        {{ $story->title }}
                    </a>
                    @if($story->relationLoaded('categories') && $story->categories->isNotEmpty())
                        <div class="tmnc-list-meta text-muted small mt-0 color-2">
                            {{ $story->categories->pluck('name')->take(3)->implode(', ') }}
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
    
        <div class="text-center mt-2 d-md-none">
            <a href="{{ route('story.new.chapter') }}" class="btn btn-sm btn-outline-secondary">Xem thêm &raquo;</a>
        </div>
    </div>
</section>

@push('styles')
<style>
.tmnc-simple-list { border: none; }
.tmnc-list-item { border: none; }
.tmnc-list-item-border {
    border-bottom: 1px solid rgba(0,0,0,0.1);
}
body.dark-mode .tmnc-list-item-border { border-color: #404040; }
.tmnc-list-title {
    font-size: 0.95rem;
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.tmnc-list-title:hover { color: var(--primary-color-3) !important; }
.tmnc-list-meta { font-size: 0.8rem; }
body.dark-mode .tmnc-list-title { color: #e0e0e0; }
body.dark-mode .tmnc-list-title:hover { color: var(--primary-color-3) !important; }
body.dark-mode .bg-list { background-color: #2d2d2d !important; }
</style>
@endpush
