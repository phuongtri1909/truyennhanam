@extends('layouts.app')

@section('title', 'Dịch giả ' . $translator->name)
@section('description', 'Truyện dịch bởi ' . $translator->name . ' tại ' . config('app.name'))

@section('content')
<div class="pt-5 container-xl">
    <div class="row g-4">
        <div class="col-12 col-md-4 col-lg-3">
            <div class="translator-info-card bg-white rounded-3 shadow-sm border p-4">
                <h3 class="h5 fw-bold text-dark mb-3">Dịch giả</h3>
                <div class="text-center mb-3">
                    <div class="translator-avatar-wrap mx-auto rounded-circle overflow-hidden bg-light mb-3">
                        @if($translator->avatar)
                            <img src="{{ Storage::url($translator->avatar) }}" alt="{{ $translator->name }}" class="w-100 h-100 object-fit-cover">
                        @else
                            <div class="d-flex align-items-center justify-content-center w-100 h-100 text-dark fw-bold" style="min-height: 120px; font-size: 2.5rem;">
                                {{ strtoupper(mb_substr($translator->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <h4 class="fw-bold text-dark mb-2">{{ $translator->name }}</h4>
                    <a href="{{ route('user.donate', ['recipient' => $translator->id]) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-heart me-1"></i>Donate
                    </a>
                </div>
                <div class="translator-desc text-muted small">
                    {{ $translator->bio ?? 'Đây là phần mô tả' }}
                </div>
            </div>
        </div>

        <div class="col-12 col-md-8 col-lg-9">
            <div class="translator-stories-section">
                <div class="d-flex justify-content-between align-items-center py-3 mb-0">
                    <h2 class="m-0 fs-5 fw-bold text-dark d-flex align-items-center gap-2 font-svn-apple">
                        <span class="translator-header-bar"></span>
                        <span class="color-3">Truyện phụ trách</span>
                    </h2>
                </div>
                <div class="px-0 py-4 bg-white rounded">
                    <div class="translator-stories-grid">
                        @forelse ($stories as $story)
                            <div class="translator-story-card">
                                <a href="{{ route('show.page.story', $story->slug) }}" class="translator-story-item d-flex text-decoration-none p-2 rounded">
                                    <div class="translator-story-thumb flex-shrink-0 position-relative">
                                        <img src="{{ $story->cover ? Storage::url($story->cover) : asset('assets/images/story_default.jpg') }}"
                                            alt="{{ $story->title }}" class="translator-story-img">
                                        <span class="translator-story-overlay">{{ Str::upper(Str::limit($story->title, 25)) }}</span>
                                        @if ($story->completed)
                                            <span class="translator-badge-full">Full</span>
                                        @endif
                                    </div>
                                    <div class="translator-story-body flex-grow-1 min-w-0 ps-3">
                                        <h6 class="translator-story-title mb-2 text-dark fw-bold">
                                            {{ $story->title }}
                                        </h6>
                                        @if ($story->categories && $story->categories->isNotEmpty())
                                            <div class="translator-story-tags d-flex flex-wrap gap-1 mb-2">
                                                @foreach ($story->categories->take(3) as $cat)
                                                    <span class="translator-story-tag">{{ $cat->name }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                        <p class="translator-story-desc mb-0">
                                            {{ cleanDescription($story->description ?? '', 400) ?: 'Đang cập nhật...' }}
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <div class="translator-story-empty text-center py-4 text-muted">
                                <i class="fas fa-book-open fa-2x mb-2"></i>
                                <p class="mb-0">Chưa có truyện nào.</p>
                            </div>
                        @endforelse
                    </div>
                    @if ($stories->hasPages())
                        <div class="mt-4">
                            <x-pagination :paginator="$stories" />
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .translator-avatar-wrap { width: 120px; height: 120px; }
    .translator-info-card { border-color: rgba(0,0,0,0.06) !important; }
    .translator-header-bar { width: 4px; height: 1.2em; background: var(--primary-color-2); border-radius: 2px; }

    .translator-stories-grid {
        display: grid;
        gap: 0.75rem;
    }
    @media (min-width: 768px) {
        .translator-stories-grid {
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
    }
    .translator-story-empty { grid-column: 1 / -1; }

    .translator-story-item { transition: opacity 0.2s; }
    .translator-story-item:hover { opacity: 0.9; }
    .translator-story-thumb {
        width: 90px;
        height: 120px;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .translator-story-img { width: 100%; height: 100%; object-fit: cover; }
    .translator-story-overlay {
        position: absolute;
        top: 0; left: 0; right: 0;
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
    .translator-badge-full {
        position: absolute;
        bottom: 0; left: 0;
        background: var(--success-color, #28a745);
        color: #fff;
        font-size: 0.65rem;
        font-weight: 600;
        padding: 2px 6px;
        z-index: 2;
    }
    .translator-story-title {
        font-size: 0.95rem;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .translator-story-tag {
        display: inline-block;
        padding: 2px 8px;
        color: #6b7280;
        font-size: 0.7rem;
        border-radius: 4px;
        border: 1px solid var(--primary-color-1);
    }
    .translator-story-desc {
        font-size: 0.8rem;
        line-height: 1.5;
        color: #000;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 6;
        -webkit-box-orient: vertical;
    }
    @media (min-width: 768px) {
        .translator-story-thumb { width: 100px; height: 135px; }
    }
    body.dark-mode .translator-info-card { background-color: #2d2d2d !important; border-color: #404040 !important; }
    body.dark-mode .translator-desc { color: #9ca3af; }
    body.dark-mode .translator-story-title { color: #e0e0e0 !important; }
    body.dark-mode .translator-story-tag { background: #404040; color: #9ca3af; }
    body.dark-mode .translator-story-desc { color: #9ca3af; }
</style>
@endpush
@endsection
