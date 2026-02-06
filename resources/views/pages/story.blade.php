@extends('layouts.app')

@section('title', $story->title . ' - Truyện ' . config('app.name') . ' - Đọc Truyện Online Miễn Phí | ' .
    config('app.name'))

@section('description', Str::limit(html_entity_decode(strip_tags($story->description)), 160))

@section('keyword',
    implode(', ', [
    $story->title,
    'đọc truyện ' . $story->title,
    'truyện online',
    $story->categories->pluck('name')->implode(', '),
    $story->user->name ?? 'tác giả',
    'Truyện ' .
    config('app.name') .
    ' - Đọc Truyện
    Online Miễn Phí',
    $story->completed ? 'truyện hoàn thành' : 'truyện đang cập nhật',
    'novel',
    'web đọc truyện',
    ]))

@section('meta')
    <meta property="og:type" content="book">
    <meta property="og:title" content="{{ $story->title }} - {{ config('app.name') }}">
    <meta property="og:description" content="{{ Str::limit(html_entity_decode(strip_tags($story->description)), 160) }}">
    <meta property="og:image"
        content="{{ $story->cover ? url(Storage::url($story->cover)) : url(asset('images/logo/logo-site.png')) }}">
    <meta property="og:image:secure_url"
        content="{{ $story->cover ? url(Storage::url($story->cover)) : url(asset('images/logo/logo-site.png')) }}">
    <meta property="og:image:width" content="600">
    <meta property="og:image:height" content="800">
    <meta property="og:image:alt" content="Ảnh bìa truyện {{ $story->title }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:locale" content="vi_VN">
    <meta property="og:updated_time" content="{{ $story->updated_at->format('c') }}">

    {{-- Twitter Card Meta Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $story->title }} - {{ config('app.name') }}">
    <meta name="twitter:description" content="{{ Str::limit(html_entity_decode(strip_tags($story->description)), 160) }}">
    <meta name="twitter:image"
        content="{{ $story->cover ? url(Storage::url($story->cover)) : url(asset('images/logo/logo-site.png')) }}">
    <meta name="twitter:image:alt" content="Ảnh bìa truyện {{ $story->title }}">

    {{-- Additional Book Meta Tags --}}
    <meta property="book:author" content="{{ $story->author_name ?? ($story->user->name ?? 'Unknown') }}">
    <meta property="book:isbn" content="">
    <meta property="book:release_date" content="{{ $story->created_at->format('Y-m-d') }}">
    @foreach ($story->categories as $category)
        <meta property="book:tag" content="{{ $category->name }}">
    @endforeach
@endsection

@push('styles')
    <style>
        .card-search {
            background: #e7e7e7;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .search-wrapper {
            max-width: min(500px, 90%);
            margin: 0 auto;
        }

        .search-wrapper .form-control {
            height: 50px;
            border-radius: 25px 0 0 25px;
            border: none;
            padding-left: 20px;
        }

        .search-wrapper .btn {
            border-radius: 0 25px 25px 0;
            padding: 0 25px;
        }

        @media (max-width: 768px) {
            .search-wrapper .form-control {
                height: 40px;
            }

            .search-wrapper .btn {
                padding: 0 15px;
            }
        }

        .banner-image-home {
            width: 100%;
            height: 350px;
            object-position: center;
        }

        @media (max-width: 768px) {
            .banner-image-home {
                height: 250px;
            }
        }

        @media (max-width: 576px) {
            .banner-image-home {
                height: 200px;
            }
        }

        /* Dark mode styles for story page */
        body.dark-mode .card-search {
            background-color: #404040 !important;
            color: #e0e0e0 !important;
        }

        body.dark-mode .form-control {
            background-color: #2d2d2d !important;
            border-color: #555 !important;
            color: #e0e0e0 !important;
        }

        body.dark-mode .form-control:focus {
            background-color: #2d2d2d !important;
            border-color: var(--primary-color-3) !important;
            color: #e0e0e0 !important;
            box-shadow: 0 0 0 0.2rem rgba(57, 205, 224, 0.25) !important;
        }

        body.dark-mode .form-control::placeholder {
            color: rgba(224, 224, 224, 0.6) !important;
        }

        body.dark-mode .text-danger {
            color: #f1aeb5 !important;
        }
    </style>
@endpush

@once
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css">
<style>
    .section-title-bar { width: 4px; height: 1.25em; background: var(--primary-color-2); border-radius: 2px; }
    .section-title-tdc { font-size: 1.25rem; }
    .story-you-may-like-swiper-wrap { position: relative; }
    .story-you-may-like-swiper-wrap .storyYouMayLikeSwiper { height: auto !important; }
    .story-you-may-like-swiper-wrap .storyYouMayLikeSwiper .swiper-wrapper { align-items: stretch; box-sizing: border-box; }
    .story-you-may-like-swiper-wrap .swiper-slide { height: auto !important; box-sizing: border-box; }
    .story-you-may-like-slide-item { min-width: 0; border-radius: 8px; overflow: hidden; }
    /* Ẩn title overlay và label Full trong section này (chỉ hiện ảnh bìa như yêu cầu) */
    .story-you-may-like .story-title-overlay,
    .story-you-may-like .full-label { display: none !important; }
    .storyYouMayLikeSwiper .swiper-button-next,
    .storyYouMayLikeSwiper .swiper-button-prev {
        width: 40px; height: 40px; background: rgba(255,255,255,0.9); border-radius: 50%;
        color: var(--primary-color-3); box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .storyYouMayLikeSwiper .swiper-button-next:hover,
    .storyYouMayLikeSwiper .swiper-button-prev:hover {
        background: var(--primary-color-3); color: white; transform: scale(1.1);
    }
    .storyYouMayLikeSwiper .swiper-button-next:after,
    .storyYouMayLikeSwiper .swiper-button-prev:after { font-size: 18px; font-weight: bold; }
    body.dark-mode .storyYouMayLikeSwiper .swiper-button-next,
    body.dark-mode .storyYouMayLikeSwiper .swiper-button-prev {
        background: rgba(45,45,45,0.9) !important; color: var(--primary-color-3) !important;
    }
</style>
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
<script>
    if (document.querySelector('.storyYouMayLikeSwiper')) {
        new Swiper('.storyYouMayLikeSwiper', {
            slidesPerView: 2,
            spaceBetween: 10,
            autoHeight: true,
            observer: true,
            observeParents: true,
            loop: document.querySelectorAll('.storyYouMayLikeSwiper .swiper-slide').length > 4,
            navigation: { nextEl: '.storyYouMayLikeSwiper .swiper-button-next', prevEl: '.storyYouMayLikeSwiper .swiper-button-prev' },
            breakpoints: {
                320: { slidesPerView: 4, spaceBetween: 8 },
                540: { slidesPerView: 4, spaceBetween: 10 },
                768: { slidesPerView: 4, spaceBetween: 16 },
                992: { slidesPerView: 6, spaceBetween: 16 },
                1200: { slidesPerView: 8, spaceBetween: 16 },
            },
            speed: 800,
        });
    }
</script>
@endpush
@endonce

@section('content')
    <section id="page-story" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-8">
                    @include('components.info_book_home')

                    <div class=" mt-4">


                        @if (!(isset($story) && $story->has_combo && ($story->story_type ?? 'normal') === 'normal'))
                            @include('components.latest_chapters', ['latestChapters' => $latestChapters])
                        @endif

                        <div class="" id="chapters">
                            @if (!Auth()->check() || (Auth()->check() && Auth()->user()->ban_read == false))
                                @include('components.all_chapter', [
                                    'chapters' => $chapters,
                                    'story' => $story,
                                    'chapterPurchaseStatus' => $chapterPurchaseStatus,
                                    'showComboButton' => isset($story) && $story->has_combo && ($story->story_type ?? 'normal') === 'normal',
                                ])
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-sad-tear fa-4x text-muted mb-3 animate__animated animate__shakeX"></i>
                                    <h5 class="text-danger">Bạn đã bị cấm đọc truyện!</h5>
                                </div>
                            @endif
                        </div>

                        


                        {{-- @include('components.list_story_de_xuat', ['featuredStories' => $featuredStories]) --}}
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    @include('components.related_stories', [
                        'story' => $story,
                        'translatorHotStories' => $translatorHotStories ?? collect(),
                    ])
                </div>
            </div>

            @if(isset($relatedStories) && $relatedStories->isNotEmpty())
                <section class="story-you-may-like mt-5 mb-4">
                    <h2 class="section-title-tdc d-flex align-items-center gap-2 mb-4 m-0 fw-bold color-3">
                        <span class="section-title-bar"></span>
                        Truyện có thể bạn sẽ thích
                    </h2>
                    <div class="story-you-may-like-swiper-wrap position-relative">
                        <div class="swiper storyYouMayLikeSwiper">
                            <div class="swiper-wrapper">
                                @foreach($relatedStories as $relatedStory)
                                    <div class="swiper-slide">
                                        <div class="story-you-may-like-slide-item">
                                            @include('components.stories-grid', ['story' => $relatedStory])
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>
                    </div>
                </section>
            @endif

            <div class="" id="comments">
                @if (!Auth()->check() || (Auth()->check() && Auth()->user()->ban_comment == false))
                    @include('components.comment', [
                        'story' => $story,
                        'pinnedComments' => $pinnedComments,
                        'regularComments' => $regularComments,
                    ])
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-sad-tear fa-4x text-muted mb-3 animate__animated animate__shakeX"></i>
                        <h5 class="text-danger">Bạn đã bị cấm bình luận!</h5>
                    </div>
                @endif
            </div>
        </div>


    </section>

    @auth
        @include('components.modals.chapter-purchase-modal')
    @endauth

@endsection

@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if ($story->is_18_plus)
                const storyWarningKey = 'story_18_warning_{{ $story->id }}';

                if (!localStorage.getItem(storyWarningKey)) {
                    Swal.fire({
                        icon: 'warning',
                        title: '⚠️ Cảnh báo nội dung 18+',
                        html: `
                        <div class="text-start">
                            <p><strong>Truyện này chứa nội dung dành cho người từ 18 tuổi trở lên:</strong></p>
                            <ul class="text-start" style="margin-left: 20px;">
                                <li>Nội dung bạo lực, tình dục</li>
                                <li>Ngôn từ không phù hợp với trẻ em</li>
                                <li>Tình tiết người lớn</li>
                            </ul>
                            <p class="text-danger"><strong>Bạn có đủ 18 tuổi và muốn tiếp tục đọc?</strong></p>
                        </div>
                    `,
                        showCancelButton: true,
                        confirmButtonText: '✅ Tôi đủ 18 tuổi',
                        cancelButtonText: '❌ Quay lại',
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        customClass: {
                            popup: 'animate__animated animate__fadeInDown',
                            confirmButton: 'btn btn-danger fw-bold',
                            cancelButton: 'btn btn-secondary fw-bold'
                        },
                        backdrop: `
                        rgba(0,0,0,0.8)
                        left top
                        no-repeat
                    `
                    }).then((result) => {
                        if (result.isConfirmed) {
                            localStorage.setItem(storyWarningKey, 'confirmed');

                            Swal.fire({
                                icon: 'success',
                                title: 'Đã xác nhận',
                                text: 'Chúc bạn đọc truyện vui vẻ!',
                                timer: 1500,
                                showConfirmButton: false,
                                customClass: {
                                    popup: 'animate__animated animate__fadeInUp'
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'info',
                                title: 'Đã hủy',
                                text: 'Bạn sẽ được chuyển về trang chủ',
                                timer: 2000,
                                showConfirmButton: false,
                                customClass: {
                                    popup: 'animate__animated animate__fadeOut'
                                }
                            }).then(() => {
                                window.location.href = '{{ route('home') }}';
                            });
                        }
                    });
                }
            @endif
        });
    </script> --}}
@endpush
