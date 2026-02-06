@php
    $inline = $inline ?? false;
    $showCombo = isset($story) && ($story->story_type ?? 'normal') === 'normal' && $story->has_combo && (!auth()->check() || !\App\Models\StoryPurchase::hasUserPurchased(auth()->id(), $story->id));
@endphp
@if ($showCombo)
    @if ($inline)
        @guest
            <a href="{{ route('login') }}" class="combo-compact__btn btn border rounded-pill px-3 py-2 text-decoration-none">
                Mở truyện
            </a>
        @else
            <button type="button" class="combo-compact__btn btn border rounded-pill px-3 py-2 fw-semibold"
            onclick="showPurchaseModal('story', {{ $story->id }}, {{ json_encode($story->title) }}, {{ $story->combo_price }}, null, null, null, {{ $story->total_chapter_price ?? 0 }}, {{ $story->chapters_count ?? 0 }})">
                Mở truyện
            </button>
        @endguest
    @else
    <div class="combo-compact animate__animated animate__fadeIn">
        <div class="combo-compact__inner d-flex align-items-center justify-content-between flex-wrap gap-2 py-2 px-3">
            <span class="combo-compact__label text-dark">Mở full truyện</span>
            @guest
                <a href="{{ route('login') }}" class="combo-compact__btn btn border rounded-pill px-3 py-2 text-decoration-none">
                    Mở truyện
                </a>
            @else
                <button type="button" class="combo-compact__btn btn border rounded-pill px-3 py-2 fw-semibold"
                    onclick="showPurchaseModal('story', {{ $story->id }}, {{ json_encode($story->title) }}, {{ $story->combo_price }}, null, null, null, {{ $story->total_chapter_price ?? 0 }}, {{ $story->chapters_count ?? 0 }})">
                    Mở truyện
                </button>
            @endguest
        </div>
    </div>
    @endif
@endif

@once
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
        <style>
            .combo-compact {
                margin: 1rem 0;
            }
            .combo-compact__inner {
                background-color: #f5f4f0;
                border-radius: 10px;
                border: 1px solid #e8e6e0;
            }
            .combo-compact__label {
                font-size: 0.95rem;
            }
            .combo-compact__btn {
                border: 2px solid var(--primary-color-3) !important;
                color: var(--primary-color-3);
                font-size: 0.9rem;
                transition: background-color 0.2s ease, border-color 0.2s ease;
            }
            .combo-compact__btn:hover {
                background-color: #e0ddd6 !important;
                border-color: #c4bfb5 !important;
                color: #222 !important;
            }
            body.dark-mode .combo-compact__inner {
                background-color: #2d2d2d;
                border-color: #404040;
            }
            body.dark-mode .combo-compact__label {
                color: #e0e0e0;
            }
            body.dark-mode .combo-compact__btn {
                background-color: #383838 !important;
                color: #e0e0e0 !important;
                border-color: #505050 !important;
            }
            body.dark-mode .combo-compact__btn:hover {
                background-color: #404040 !important;
                border-color: #606060 !important;
                color: #fff !important;
            }
        </style>
    @endpush
@endonce
