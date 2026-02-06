<section class="thinh-hanh-section mt-4 mt-md-0">
    <h2 class="m-0 text-dark fw-bold title-dark fs-5 font-svn-apple mb-3 color-3">Thịnh hành</h2>

    <div class="thinh-hanh-grid row g-2">
        @foreach ($weeklyTopPurchased as $story)
            <div class="col-12 col-md-6">
                <a href="{{ route('show.page.story', $story->slug) }}" class="thinh-hanh-card d-flex text-decoration-none h-100 bg-white p-3 rounded">
                        <div class="thinh-hanh-thumb flex-shrink-0">
                            <img src="{{ $story->cover ? Storage::url($story->cover) : asset('assets/images/story_default.jpg') }}"
                                alt="{{ $story->title }}" class="thinh-hanh-img">
                        </div>
                        <div class="thinh-hanh-body flex-grow-1 px-3 min-w-0">
                            <h5 class="thinh-hanh-title mb-3 text-dark fw-bold">
                                {{ $story->title }}
                            </h5>
                            <p class="thinh-hanh-desc mb-0 text-dark fs-6">
                                {{ cleanDescription($story->description ?? '', 400) ?: 'Đang cập nhật...' }}
                            </p>
                        </div>
                    </a>
                </div>
        @endforeach
    </div>
</section>

@push('styles')
    <style>
        .thinh-hanh-card {
            background: #fff;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
            transition: all 0.2s;
            border: 1px solid rgba(0, 0, 0, 0.08);
        }

        .thinh-hanh-card:hover {
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.12);
        }

        .thinh-hanh-thumb {
            width: 110px;
            height: 150px;
            display: block;
            overflow: hidden;
            flex-shrink: 0;
            border-radius: 0.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .thinh-hanh-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .thinh-hanh-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .thinh-hanh-title {
            font-size: 1rem;
            line-height: 1.4;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .thinh-hanh-desc {
            font-size: 0.8rem;
            line-height: 1.5;
            color: #6b7280;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 6;
            -webkit-box-orient: vertical;
        }

        body.dark-mode .thinh-hanh-card {
            background: #2d2d2d;
            border-color: #404040;
        }

        body.dark-mode .thinh-hanh-title {
            color: #e0e0e0 !important;
        }

        body.dark-mode .thinh-hanh-desc {
            color: #9ca3af;
        }
    </style>
@endpush
