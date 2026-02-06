<section class="mt-5">
    <div class="completed-section">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center py-3 completed-header">
            <h2 class="m-0 fs-5 fw-bold text-dark d-flex align-items-center gap-2 font-svn-apple">
                <span class="completed-header-bar"></span>
                <span class="color-3">Đã hoàn</span>
            </h2>
            <a class="color-2 text-decoration-none small" href="{{ route('story.completed') }}">Xem thêm &raquo;</a>
        </div>

        <!-- Grid: desktop 2 cột 8 truyện, mobile 1 cột 3 truyện -->
        <div class="px-3 px-md-4 py-4 bg-white rounded">
            <div class="completed-grid">
                @forelse ($completedStories->take(8) as $story)
                    <div class="completed-grid-item">
                        @include('components.item-story-full', ['story' => $story])
                    </div>
                @empty
                    <div class="completed-grid-item completed-grid-empty text-center py-4 text-muted">
                        <i class="fas fa-book-open fa-2x mb-2"></i>
                        <p class="mb-0">Chưa có truyện hoàn thành.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    .completed-header-bar {
        width: 4px;
        height: 1.2em;
        background: var(--primary-color-2);
        border-radius: 2px;
    }
    .completed-section {
        border-color: rgba(0,0,0,0.06) !important;
    }
    .completed-grid {
        display: grid;
        gap: 0.75rem;
    }
    @media (min-width: 768px) {
        .completed-grid {
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
    }
    .completed-grid-empty {
        grid-column: 1 / -1;
    }
    body.dark-mode .completed-section {
        background-color: #2d2d2d !important;
        border-color: #404040 !important;
    }
    /* Mobile: chỉ hiện 3 truyện */
    @media (max-width: 767.98px) {
        .completed-grid .completed-grid-item:nth-child(n+4) {
            display: none;
        }
    }
</style>
@endpush
