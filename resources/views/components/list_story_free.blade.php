<section class="truyen-mien-phi-section mt-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="section-title-tdc d-flex align-items-center gap-2 m-0 fw-bold font-svn-apple color-3">
            <span class="section-title-bar"></span>
            Truyện miễn phí
        </h2>
        <a class="color-2 text-decoration-none" href="{{ route('story.free') }}">Xem thêm &raquo;</a>
    </div>

    <div class="free-stories-swiper-wrap">
        <div class="swiper freeStoriesSwiper">
            <div class="swiper-wrapper">
                @forelse ($zhihuStories as $story)
                    <div class="swiper-slide">
                        <div class="free-slide-item">
                            @include('components.stories-grid', ['story' => $story])
                        </div>
                    </div>
                @empty
                    <div class="swiper-slide">
                        <div class="alert alert-info text-center py-4 mb-4">
                            <i class="fas fa-book-open fa-2x mb-3 text-muted"></i>
                            <h5 class="mb-1">Chưa có truyện miễn phí</h5>
                        </div>
                    </div>
                @endforelse
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>
</section>

@once
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css">
<style>
    .section-title-bar { width: 4px; height: 1.25em; background: var(--primary-color-2); border-radius: 2px; }
    .section-title-tdc { font-size: 1.25rem; }
    .section-link-tdc { font-size: 0.95rem; }
    .free-stories-swiper-wrap { position: relative; }
    .free-slide-item { min-width: 0; }
    .freeStoriesSwiper .swiper-button-next,
    .freeStoriesSwiper .swiper-button-prev {
        width: 40px; height: 40px; background: rgba(255,255,255,0.9); border-radius: 50%;
        color: var(--primary-color-3); box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .freeStoriesSwiper .swiper-button-next:hover,
    .freeStoriesSwiper .swiper-button-prev:hover {
        background: var(--primary-color-3); color: white; transform: scale(1.1);
    }
    .freeStoriesSwiper .swiper-button-next:after,
    .freeStoriesSwiper .swiper-button-prev:after { font-size: 18px; font-weight: bold; }
    body.dark-mode .freeStoriesSwiper .swiper-button-next,
    body.dark-mode .freeStoriesSwiper .swiper-button-prev {
        background: rgba(45,45,45,0.9) !important; color: var(--primary-color-3) !important;
    }
</style>
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
<script>
    if (document.querySelector('.freeStoriesSwiper')) {
        new Swiper('.freeStoriesSwiper', {
            slidesPerView: 2, spaceBetween: 10,
            loop: {{ ($zhihuStories->count() ?? 0) > 4 ? 'true' : 'false' }},
            navigation: { nextEl: '.freeStoriesSwiper .swiper-button-next', prevEl: '.freeStoriesSwiper .swiper-button-prev' },
            breakpoints: {
                320: { slidesPerView: 4, spaceBetween: 8 },
                540: { slidesPerView: 4, spaceBetween: 10 },
                768: { slidesPerView: 4, spaceBetween: 16 },
                992: { slidesPerView: 6, spaceBetween: 16 },
                1200: { slidesPerView: 8, spaceBetween: 16 },
            },
            autoplay: {{ ($zhihuStories->count() ?? 0) > 4 ? 'true' : 'false' }} ? { delay: 300000, disableOnInteraction: false } : false,
            speed: 800,
        });
    }
</script>
@endpush
@endonce
