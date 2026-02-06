<div class="story-card">
    <div class="story-thumbnail">
        <a href="{{ route('show.page.story', $story->slug) }}">
            <img src="{{ $story->cover ? Storage::url($story->cover) : asset('assets/images/story_default.jpg') }}"
                alt="{{ $story->title }}" class="img-fluid">
            <span class="story-title-overlay">{{ $story->title }}</span>
            @if ($story->is_18_plus === 1)
                @include('components.tag18plus')
            @endif



            @if (isset($story->featured_time_remaining) && $story->featured_time_remaining > 0)
                <div class="featured-timer">
                    <div class="timer-triangle">
                        <i class="fas fa-clock timer-icon"></i>
                        <span class="timer-text">{{ $story->featured_time_remaining }}h</span>
                    </div>
                </div>
            @endif
        </a>

    </div>
    <div class="story-info text-sm text-gray-600 fw-semibold d-none">
        <div>
            <h5 class="story-title mb-0 text-sm fw-semibold lh-base text-center">
                <a href="{{ route('show.page.story', $story->slug) }}"
                    class="text-decoration-none text-dark fw-bold fs-6 text-center">
                    {{ $story->title }}
                </a>
            </h5>
        </div>
    </div>
    @if ($story->completed === 1)
        <span class="full-label"></span>
    @endif
</div>

@once
    @push('styles')
        <style>
            .story-title {
                height: 3em !important;
                overflow: hidden !important;
                display: -webkit-box !important;
                -webkit-line-clamp: 2 !important;
                -webkit-box-orient: vertical !important;
            }

            .story-thumbnail {
                position: relative;
                padding-top: 140%;
                overflow: hidden;
                border-radius: inherit;
            }

            .story-thumbnail a {
                display: block;
                position: absolute;
                inset: 0;
            }

            .story-title-overlay {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                min-height: 41px;
                padding: 3px;
                background: linear-gradient(to top, rgba(0, 0, 0, 0.92) 0%, rgba(0, 0, 0, 0.65) 50%, transparent 100%);
                color: #fff;
                font-size: 0.75rem;
                font-weight: 600;
                line-height: 1.45;
                overflow: hidden;
                display: -webkit-box;
                -webkit-line-clamp: 3;
                -webkit-box-orient: vertical;
                text-overflow: ellipsis;
                z-index: 2;
                box-sizing: border-box;
                text-align: center;
            }

            .story-thumbnail img {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.3s ease;
            }

            .badge-full {
                position: absolute;
                top: 8px;
                right: 8px;
                background-color: var(--success-color, #28a745) !important;
                color: white;
                padding: 3px 8px;
                border-radius: 4px;
                font-size: 0.7rem;
                font-weight: 600;
                z-index: 3;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            }

            .featured-timer {
                position: absolute;
                top: 0;
                left: 0;
                z-index: 3;
            }

            .timer-triangle {
                width: 0;
                height: 0;
                border-style: solid;
                border-width: 50px 50px 0 0;
                border-color: #3b82f6 transparent transparent transparent;
                position: relative;
            }

            .timer-icon {
                position: absolute;
                top: -45px;
                left: 5px;
                color: white;
                font-size: 0.6rem;
                z-index: 4;
                transform: rotate(-45deg);
            }

            .timer-text {
                position: absolute;
                top: -36px;
                left: 9px;
                color: white;
                font-size: 0.7rem;
                font-weight: 600;
                z-index: 4;
                transform: rotate(-45deg);
                line-height: 1;
            }

            .hover-content {
                color: white;
                text-align: center;
                transform: translateY(10px);
                transition: transform 0.3s ease;
            }

            .story-card {
                overflow: visible;
                transition: all 0.3s ease;
                height: 100%;
                position: relative;
                z-index: 1;
            }

            .story-card:hover {
                z-index: 10;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            }

            .story-card:hover .story-thumbnail img {
                transform: scale(1.05);
            }

            .story-categories {
                display: flex;
                flex-wrap: wrap;
                gap: 4px;
                justify-content: center;
                margin-top: 8px;
            }

            .story-title {
                height: 3em;
                overflow: hidden;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
            }

            .category-badge {
                background: rgba(255, 255, 255, 0.2);
                color: white;
                padding: 3px 10px;
                border-radius: 12px;
                font-size: 0.75rem;
                backdrop-filter: blur(4px);
                transition: all 0.3s ease;
            }

            .category-badge:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: translateY(-1px);
            }

            .story-views-time-container {
                display: flex;
                align-items: center;
                white-space: nowrap;
                justify-content: space-between;
                gap: 8px;
            }

            .story-views {
                flex-shrink: 0;
            }

            .story-time {
                flex-shrink: 0;
                text-align: right;
                white-space: nowrap;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    @endpush
@endonce
