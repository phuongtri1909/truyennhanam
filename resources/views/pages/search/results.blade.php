@extends('layouts.app')

@section('title')
    @if (isset($isSearch) && $isSearch)
        @if (isset($searchType))
            @if ($searchType === 'author')
                Tìm truyện của tác giả: {{ $query }}
            @elseif($searchType === 'translator')
                Tìm truyện dịch bởi: {{ $query }}
            @else
                Kết quả tìm kiếm: {{ $query }}
            @endif
        @else
            Kết quả tìm kiếm: {{ $query }}
        @endif
    @elseif(isset($query) && $query === 'new-chapter')
        Truyện mới nhất
    @elseif(isset($query) && $query === 'hot')
        Truyện đề cử
    @elseif(isset($query) && $query === 'rating')
        Truyện được đánh giá cao
    @elseif(isset($query) && $query === 'view')
        Truyện được xem nhiều
    @elseif(isset($query) && $query === 'follow')
        Truyện được theo dõi nhiều
    @elseif(isset($query) && $query === 'completed')
        Truyện đã hoàn thành
    @elseif(isset($query) && $query === 'free')
        Truyện miễn phí
    @elseif(isset($query) && $query === 'new')
        Truyện mới
    @else
        @if(isset($currentCategory))
            {{ $currentCategory->name }}
        @else
            Danh sách truyện
        @endif
    @endif
@endsection

@section('description')
    @if (isset($isSearch) && $isSearch)
        @if (isset($searchType))
            @if ($searchType === 'author')
                Danh sách truyện của tác giả "{{ $query }}" tại {{ config('app.name') }}
            @elseif($searchType === 'translator')
                Danh sách truyện được dịch bởi "{{ $query }}" tại {{ config('app.name') }}
            @else
                Kết quả tìm kiếm cho "{{ $query }}" tại {{ config('app.name') }}
            @endif
        @else
            Kết quả tìm kiếm cho "{{ $query }}" tại {{ config('app.name') }}
        @endif
    @elseif(isset($query) && $query === 'new-chapter')
        Truyện mới nhất tại
    @elseif(isset($query) && $query === 'hot')
        Truyện đề cử tại
    @elseif(isset($query) && $query === 'rating')
        Truyện được đánh giá cao tại
    @elseif(isset($query) && $query === 'view')
        Truyện được xem nhiều tại
    @elseif(isset($query) && $query === 'follow')
        Truyện được theo dõi nhiều tại
    @elseif(isset($query) && $query === 'completed')
        Truyện đã hoàn thành tại
    @elseif(isset($query) && $query === 'free')
        Truyện miễn phí tại
    @elseif(isset($query) && $query === 'new')
        Truyện mới tại
    @else
        @if(isset($currentCategory))
            Truyện thể loại {{ $currentCategory->name }} tại {{ config('app.name') }}
        @else
            Danh sách truyện tại {{ config('app.name') }}
        @endif
    @endif
@endsection

@section('content')
    <div class="pt-5 container">
        <!-- Mobile Filter Toggle Button -->
        <div class="d-md-none mb-3">
            <button class="btn w-100" type="button" id="mobileFilterToggle">
                <i class="fa-solid fa-filter me-2"></i>
                <span>Tìm kiếm nâng cao</span>
                <i class="fa-solid fa-chevron-down ms-auto" id="filterToggleIcon"></i>
            </button>
        </div>

        <div class="row">
            <!-- Advanced Search Sidebar (4 columns) -->
            <div class="col-12 col-md-4 col-lg-3" id="advancedSearchSidebar">
                <div class="d-none d-md-block">
                    @include('components.advanced-search', [
                        'searchUrl' => $searchUrl ?? '#',
                        'searchType' => $searchType ?? null,
                        'categories' => $categories ?? collect(),
                        'currentQuery' => $displayQuery ?? '',
                    ])
                </div>
                <!-- Mobile Advanced Search (Hidden by default) -->
                <div class="d-md-none" id="mobileAdvancedSearch" style="display: none;">
                    @include('components.advanced-search', [
                        'searchUrl' => $searchUrl ?? '#',
                        'searchType' => $searchType ?? null,
                        'categories' => $categories ?? collect(),
                        'currentQuery' => $displayQuery ?? '',
                    ])
                </div>
            </div>

            <!-- Main content area (7 columns) -->
            <div class="col-12 col-md-8 col-lg-9">
                <div class="bg-white p-3 rounded-4 shadow-sm mb-4">
                    <h2 class="h4 mb-3 fw-bold">
                        @if (isset($isSearch) && $isSearch)
                            @if (isset($searchType))
                                @if ($searchType === 'author')
                                    Tác giả: "{{ $query }}"
                                @elseif($searchType === 'translator')
                                    Dịch giả: "{{ $query }}"
                                @else
                                    <i class="fa-solid fa-magnifying-glass fa-lg text-warning"></i>
                                    Kết quả tìm kiếm: "{{ $query }}"
                                @endif
                            @else
                                <i class="fa-solid fa-magnifying-glass fa-lg text-warning"></i>
                                Kết quả tìm kiếm: "{{ $query }}"
                            @endif
                        @elseif(isset($query) && $query === 'new-chapter')
                            <i class="fa-solid fa-layer-group fa-lg text-primary"></i>
                            Truyện mới nhất
                        @elseif(isset($query) && $query === 'hot')
                            <i class="fa-solid fa-layer-group fa-lg text-primary"></i>
                            Truyện đề cử
                        @elseif(isset($query) && $query === 'rating')
                            <i class="fa-solid fa-layer-group fa-lg text-primary"></i>
                            Truyện được đánh giá cao
                        @elseif(isset($query) && $query === 'view')
                            <i class="fa-solid fa-layer-group fa-lg text-primary"></i>
                            Truyện được xem nhiều
                        @elseif(isset($query) && $query === 'follow')
                            <i class="fa-solid fa-layer-group fa-lg text-primary"></i>
                            Truyện được theo dõi nhiều
                        @elseif(isset($query) && $query === 'completed')
                            <i class="fa-solid fa-layer-group fa-lg text-primary"></i>
                            Truyện đã hoàn thành
                        @elseif(isset($query) && $query === 'free')
                            <i class="fa-solid fa-gift fa-lg text-primary"></i>
                            Truyện miễn phí
                        @elseif(isset($query) && $query === 'new')
                            <i class="fa-solid fa-layer-group fa-lg text-primary"></i>
                            Truyện mới
                        @elseif(isset($query) && $query === 'category')
                            <i class="fa-solid fa-layer-group fa-lg text-primary"></i>
                            @if(isset($currentCategory))
                                Truyện thể loại: {{ $currentCategory->name }}
                            @else
                                Danh sách truyện theo thể loại
                            @endif
                        @else
                            <i class="fa-solid fa-layer-group fa-lg text-primary"></i>
                            Danh sách truyện
                        @endif
                    </h2>

                    <!-- Active Filters Tags -->
                    @php
                        $activeFilters = [];
                        
                        // Search query filter
                        if (!empty($displayQuery) && trim($displayQuery) !== '') {
                            $activeFilters[] = [
                                'type' => 'query',
                                'label' => 'Từ khóa: "' . $displayQuery . '"',
                                'value' => $displayQuery,
                                'remove_url' => request()->fullUrlWithQuery(['query' => null])
                            ];
                        }
                        
                        // Category filter
                        if (request('category') && trim(request('category')) !== '') {
                            $selectedCategory = $categories->firstWhere('id', request('category'));
                            if ($selectedCategory) {
                                $activeFilters[] = [
                                    'type' => 'category',
                                    'label' => 'Thể loại: ' . $selectedCategory->name,
                                    'value' => $selectedCategory->name,
                                    'remove_url' => request()->fullUrlWithQuery(['category' => null])
                                ];
                            }
                        }
                        
                        // Sort filter
                        if (request('sort') && trim(request('sort')) !== '') {
                            $sortLabels = [
                                'newest' => 'Mới nhất',
                                'oldest' => 'Cũ nhất',
                                'most_chapters' => 'Nhiều chương nhất',
                                'least_chapters' => 'Ít chương nhất',
                                'most_views' => 'Xem nhiều nhất',
                                'highest_rating' => 'Đánh giá cao nhất'
                            ];
                            $sortLabel = $sortLabels[request('sort')] ?? request('sort');
                            $activeFilters[] = [
                                'type' => 'sort',
                                'label' => 'Sắp xếp: ' . $sortLabel,
                                'value' => $sortLabel,
                                'remove_url' => request()->fullUrlWithQuery(['sort' => null])
                            ];
                        }
                        
                        // Chapters filter
                        if (request('chapters') && trim(request('chapters')) !== '') {
                            $chaptersLabels = [
                                '1-10' => '1-10 chương',
                                '11-50' => '11-50 chương',
                                '51-100' => '51-100 chương',
                                '100+' => 'Trên 100 chương'
                            ];
                            $chaptersLabel = $chaptersLabels[request('chapters')] ?? request('chapters');
                            $activeFilters[] = [
                                'type' => 'chapters',
                                'label' => 'Số chương: ' . $chaptersLabel,
                                'value' => $chaptersLabel,
                                'remove_url' => request()->fullUrlWithQuery(['chapters' => null])
                            ];
                        }
                        
                        // Status filter
                        if (request('status') && trim(request('status')) !== '') {
                            $statusLabels = [
                                'ongoing' => 'Đang cập nhật',
                                'completed' => 'Đã hoàn thành'
                            ];
                            $statusLabel = $statusLabels[request('status')] ?? request('status');
                            $activeFilters[] = [
                                'type' => 'status',
                                'label' => 'Tình trạng: ' . $statusLabel,
                                'value' => $statusLabel,
                                'remove_url' => request()->fullUrlWithQuery(['status' => null])
                            ];
                        }
                    @endphp

                    @if (count($activeFilters) > 0)
                        <div class="active-filters mb-3">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="text-muted small me-2">
                                    <i class="fa-solid fa-filter me-1"></i>
                                    Bộ lọc đang áp dụng:
                                </span>
                                @foreach ($activeFilters as $filter)
                                    <span class="badge bg-2 text-dark position-relative pe-4">
                                        {{ $filter['label'] }}
                                        <a href="{{ $filter['remove_url'] }}" 
                                           class="position-absolute top-0 end-0 p-1 text-dark"
                                           style="font-size: 0.7rem; line-height: 1; text-decoration: none;"
                                           title="Xóa bộ lọc">
                                            <i class="fa-solid fa-times"></i>
                                        </a>
                                    </span>
                                @endforeach
                                @if (count($activeFilters) > 1)
                                    <a href="{{ request()->url() }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fa-solid fa-times me-1"></i>
                                        Xóa tất cả
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if ($stories->total() > 0)
                        <p class="text-muted small mb-3">Tìm thấy {{ $stories->total() }} truyện</p>
                    @else
                        <p class="text-muted mb-3">Không tìm thấy truyện phù hợp</p>
                    @endif

                    <div class="search-stories-grid">
                        @forelse ($stories as $story)
                            <div class="search-story-card">
                                <a href="{{ route('show.page.story', $story->slug) }}" class="search-story-item d-flex text-decoration-none p-2 rounded">
                                    <div class="search-story-thumb flex-shrink-0 position-relative">
                                        <img src="{{ $story->cover ? Storage::url($story->cover) : asset('assets/images/story_default.jpg') }}"
                                            alt="{{ $story->title }}" class="search-story-img">
                                        <span class="search-story-overlay">{{ Str::upper(Str::limit($story->title, 25)) }}</span>
                                        @if ($story->completed)
                                            <span class="search-badge-full">Full</span>
                                        @endif
                                    </div>
                                    <div class="search-story-body flex-grow-1 min-w-0 ps-3">
                                        <h6 class="search-story-title mb-2 text-dark fw-bold">
                                            {{ $story->title }}
                                        </h6>
                                        @if ($story->categories && $story->categories->isNotEmpty())
                                            <div class="search-story-tags d-flex flex-wrap gap-1 mb-2">
                                                @foreach ($story->categories->take(3) as $cat)
                                                    <span class="search-story-tag">{{ $cat->name }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                        <p class="search-story-desc mb-0">
                                            {{ cleanDescription($story->description ?? '', 400) ?: 'Đang cập nhật...' }}
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <div class="search-story-empty text-center py-5 text-muted" style="grid-column: 1 / -1;">
                                <i class="fas fa-book-open fa-3x mb-3"></i>
                                <p class="mb-0">Không tìm thấy truyện phù hợp.</p>
                            </div>
                        @endforelse
                    </div>

                    @if ($stories->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            <x-pagination :paginator="$stories" />
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>

         #mobileFilterToggle {
             display: flex;
             align-items: center;
             justify-content: space-between;
             transition: all 0.3s ease;
             border-radius: 8px;
             padding: 12px 16px;
             color: white !important;
             border: none;
             background-color: #4e8ad8;
         }

         #mobileFilterToggle:hover {
             transform: translateY(-1px);
             box-shadow: 0 2px 8px rgba(247, 148, 137, 0.3);
         }

        #mobileFilterToggle i {
            transition: transform 0.3s ease;
        }

        #mobileAdvancedSearch {
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

         @media (max-width: 767.98px) {
             #mobileAdvancedSearch .advanced-search-container {
                 margin-bottom: 1rem;
                 box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                 border-radius: 8px;
             }
         }

         /* Active Filter Tags Styles */
         .active-filters .badge {
             transition: all 0.3s ease;
             border-radius: 20px;
             padding: 8px 12px;
             font-weight: 500;
         }

         .active-filters .badge:hover {
             transform: translateY(-1px);
             box-shadow: 0 2px 8px rgba(252, 207, 212, 0.4);
         }

         .active-filters .badge a {
             transition: all 0.2s ease;
         }

         .active-filters .badge a:hover {
             color: var(--primary-color-3) !important;
             transform: scale(1.2);
         }

         .active-filters .btn-outline-secondary {
             border-radius: 20px;
             font-size: 0.8rem;
             padding: 6px 12px;
             transition: all 0.3s ease;
         }

         .active-filters .btn-outline-secondary:hover {
             background-color: var(--primary-color-2);
             border-color: var(--primary-color-3);
             color: var(--primary-color-3);
             transform: translateY(-1px);
         }

         /* Danh sách truyện - giống trang dịch giả */
         .search-stories-grid {
             display: grid;
             gap: 0.75rem;
         }
         @media (min-width: 768px) {
             .search-stories-grid {
                 grid-template-columns: 1fr 1fr;
                 gap: 1.5rem;
             }
         }
         .search-story-item { transition: opacity 0.2s; }
         .search-story-item:hover { opacity: 0.9; }
         .search-story-thumb {
             width: 90px;
             height: 120px;
             border-radius: 0.5rem;
             overflow: hidden;
         }
         .search-story-img { width: 100%; height: 100%; object-fit: cover; }
         .search-story-overlay {
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
         .search-badge-full {
             position: absolute;
             bottom: 0; left: 0;
             background: var(--success-color, #28a745);
             color: #fff;
             font-size: 0.65rem;
             font-weight: 600;
             padding: 2px 6px;
             z-index: 2;
         }
         .search-story-title {
             font-size: 0.95rem;
             overflow: hidden;
             display: -webkit-box;
             -webkit-line-clamp: 2;
             -webkit-box-orient: vertical;
         }
         .search-story-tag {
             display: inline-block;
             padding: 2px 8px;
             color: #6b7280;
             font-size: 0.7rem;
             border-radius: 4px;
             border: 1px solid var(--primary-color-1);
         }
         .search-story-desc {
             font-size: 0.8rem;
             line-height: 1.5;
             color: #000;
             overflow: hidden;
             display: -webkit-box;
             -webkit-line-clamp: 6;
             -webkit-box-orient: vertical;
         }
         @media (min-width: 768px) {
             .search-story-thumb { width: 100px; height: 135px; }
         }
         body.dark-mode .search-story-title { color: #e0e0e0 !important; }
         body.dark-mode .search-story-tag { background: #404040; color: #9ca3af; }
         body.dark-mode .search-story-desc { color: #9ca3af; }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.getElementById('mobileFilterToggle');
            const mobileSearch = document.getElementById('mobileAdvancedSearch');
            const toggleIcon = document.getElementById('filterToggleIcon');

            if (toggleButton && mobileSearch && toggleIcon) {
                toggleButton.addEventListener('click', function() {
                    const isVisible = mobileSearch.style.display !== 'none';

                    if (isVisible) {
                        // Hide the search with smooth animation
                        mobileSearch.style.opacity = '0';
                        mobileSearch.style.transform = 'translateY(-10px)';

                        setTimeout(() => {
                            mobileSearch.style.display = 'none';
                            mobileSearch.style.opacity = '1';
                            mobileSearch.style.transform = 'translateY(0)';
                        }, 300);

                         toggleIcon.classList.remove('fa-chevron-up');
                         toggleIcon.classList.add('fa-chevron-down');
                        
                    } else {
                        // Show the search with smooth animation
                        mobileSearch.style.display = 'block';
                        mobileSearch.style.opacity = '0';
                        mobileSearch.style.transform = 'translateY(-10px)';

                        setTimeout(() => {
                            mobileSearch.style.opacity = '1';
                            mobileSearch.style.transform = 'translateY(0)';
                        }, 10);

                         toggleIcon.classList.remove('fa-chevron-down');
                         toggleIcon.classList.add('fa-chevron-up');
                       
                    }
                });
            }
        });
    </script>
@endpush
