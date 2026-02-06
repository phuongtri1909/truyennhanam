@extends('layouts.information')

@section('info_title', $story->title)
@section('info_section_title', $story->title)
@section('info_section_desc', $story->author_name . ' • ' . $story->chapters_count . ' chương')

@section('info_content')
<div class="author-application-form-wrapper author-story-compact">
    <div class="author-form-card mb-4">
        <div class="row">
            <div class="col-md-3 text-center">
                <img src="{{ Storage::url($story->cover_thumbnail ?? $story->cover) }}" class="img-fluid rounded shadow-sm" style="max-height: 220px; object-fit: cover;">
                <div class="mt-3">
                    <span class="badge author-status-tag bg-{{ $story->status == 'published' ? 'success' : ($story->status == 'pending' ? 'warning' : ($story->status == 'rejected' ? 'danger' : 'secondary')) }}">
                        {{ $story->status == 'published' ? 'Hiển thị' : ($story->status == 'pending' ? 'Chờ duyệt' : ($story->status == 'rejected' ? 'Từ chối' : 'Nháp')) }}
                    </span>
                    @if($story->submitted_at)
                        <div class="small text-muted mt-2">Gửi: {{ $story->submitted_at->format('d/m/Y H:i') }}</div>
                    @endif
                </div>
            </div>
            <div class="col-md-9">
                <h5 class="mb-2 fw-bold">{{ $story->title }}</h5>
                @if($story->author_name)<p class="text-muted mb-2"><i class="fa-solid fa-user-pen me-1"></i>{{ $story->author_name }}</p>@endif
                <div class="mb-3">
                    @foreach($story->categories as $cat)
                        <span class="badge author-status-tag bg-light text-dark me-1">{{ $cat->name }}</span>
                    @endforeach
                </div>
                @if($story->status == 'rejected' && $story->admin_note)
                <div class="alert alert-warning py-2 px-3 mb-3">
                    <strong>Lý do từ chối:</strong> {{ $story->admin_note }}
                </div>
                @endif
                @if($story->status == 'pending' && $story->submitted_note)
                <div class="alert alert-info py-2 px-3 mb-3">
                    <strong>Ghi chú khi gửi:</strong> {{ $story->submitted_note }}
                </div>
                @endif

                {{-- Thống kê --}}
                <div class="row g-2 mb-3">
                    <div class="col-6 col-md-3">
                        <div class="author-form-card p-2 text-center">
                            <div class="small text-muted">Số chương</div>
                            <div class="fw-bold">{{ $story->chapters_count }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="author-form-card p-2 text-center">
                            <div class="small text-muted">Lượt xem</div>
                            <div class="fw-bold">{{ number_format($story->total_views) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="author-form-card p-2 text-center">
                            <div class="small text-muted">Theo dõi</div>
                            <div class="fw-bold">{{ $bookmarks_count }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="author-form-card p-2 text-center">
                            <div class="small text-muted">Doanh thu</div>
                            <div class="fw-bold">{{ number_format($total_revenue) }} cám</div>
                        </div>
                    </div>
                </div>

                @if($story->has_combo)
                <div class="alert alert-info py-2 px-3 mb-3">
                    <h6 class="mb-2"><i class="fa-solid fa-tag me-1"></i> Thông tin combo</h6>
                    <p class="mb-1 small">- Giá combo: <strong>{{ number_format($story->combo_price) }}</strong> cám</p>
                    <p class="mb-1 small">- Tổng giá mua lẻ: <strong>{{ number_format($story->total_chapter_price) }}</strong> cám</p>
                    <p class="mb-0 small">- Tiết kiệm: <strong>{{ number_format($story->total_chapter_price - $story->combo_price) }}</strong> cám ({{ $story->discount_percentage }}%)</p>
                </div>
                @endif

                <div class="mb-4">
                    <a href="{{ route('author.stories.chapters.index', $story) }}" class="btn author-form-submit-btn btn-sm me-2">
                        <i class="fa-solid fa-book-open me-1"></i> Quản lý chương ({{ $story->chapters_count }})
                    </a>
                    @if(in_array($story->status, ['draft', 'rejected']))
                    <form action="{{ route('author.stories.submit', $story) }}" method="POST" class="d-inline form-submit-confirm form-submit-with-note" data-message="Gửi truyện này cho admin duyệt?">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm me-2">
                            <i class="fa-solid fa-paper-plane me-1"></i> Gửi duyệt
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('author.stories.edit', $story) }}" class="btn btn-outline-warning btn-sm me-2">Sửa truyện</a>
                    <a href="{{ route('author.stories.index') }}" class="btn btn-outline-secondary btn-sm">Quay lại</a>
                </div>
                <div class="border-top pt-3">
                    <h6 class="author-form-section-title"><i class="fa-solid fa-align-left me-2"></i> Mô tả</h6>
                    <div class="author-form-info-text">{!! $story->description !!}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Lịch sử mua / Theo dõi --}}
    <ul class="nav nav-tabs mt-3 mb-2" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#author-story-purchases" role="tab">
                <i class="fa-solid fa-shopping-cart me-1"></i> Mua truyện
                <span class="badge bg-primary rounded-pill ms-1">{{ $story_purchases_count }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#author-chapter-purchases" role="tab">
                <i class="fa-solid fa-file-invoice-dollar me-1"></i> Mua chương
                <span class="badge bg-primary rounded-pill ms-1">{{ $chapter_purchases_count }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#author-bookmarks" role="tab">
                <i class="fa-solid fa-bookmark me-1"></i> Theo dõi
                <span class="badge bg-primary rounded-pill ms-1">{{ $bookmarks_count }}</span>
            </a>
        </li>
    </ul>

    <div class="tab-content author-form-card p-3">
        <div class="tab-pane active" id="author-story-purchases" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-sm table-hover author-data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Người dùng</th>
                            <th>Số tiền</th>
                            <th>Ngày mua</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($story_purchases as $purchase)
                        <tr>
                            <td>{{ $purchase->id }}</td>
                            <td>{{ $purchase->user->name ?? 'N/A' }}</td>
                            <td>{{ number_format($purchase->amount_paid) }} cám</td>
                            <td>{{ $purchase->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted">Chưa có giao dịch mua truyện</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($story_purchases->hasPages())
            <div class="d-flex justify-content-center mt-2">
                <x-pagination :paginator="$story_purchases" />
            </div>
            @endif
        </div>
        <div class="tab-pane" id="author-chapter-purchases" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-sm table-hover author-data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Người dùng</th>
                            <th>Chương</th>
                            <th>Số tiền</th>
                            <th>Ngày mua</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($chapter_purchases as $purchase)
                        <tr>
                            <td>{{ $purchase->id }}</td>
                            <td>{{ $purchase->user->name ?? 'N/A' }}</td>
                            <td>Chương {{ $purchase->chapter->number }}: {{ Str::limit($purchase->chapter->title, 25) }}</td>
                            <td>{{ number_format($purchase->amount_paid) }} cám</td>
                            <td>{{ $purchase->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted">Chưa có giao dịch mua chương</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($chapter_purchases->hasPages())
            <div class="d-flex justify-content-center mt-2">
                <x-pagination :paginator="$chapter_purchases" />
            </div>
            @endif
        </div>
        <div class="tab-pane" id="author-bookmarks" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-sm table-hover author-data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Người dùng</th>
                            <th>Chương đã đọc</th>
                            <th>Thông báo</th>
                            <th>Ngày theo dõi</th>
                            <th>Đọc gần nhất</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookmarks as $bookmark)
                        <tr>
                            <td>{{ $bookmark->id }}</td>
                            <td>{{ $bookmark->user->name ?? 'N/A' }}</td>
                            <td>
                                @if($bookmark->lastChapter)
                                    Chương {{ $bookmark->lastChapter->number }}: {{ Str::limit($bookmark->lastChapter->title, 20) }}
                                @else
                                    Chưa đọc
                                @endif
                            </td>
                            <td>
                                @if($bookmark->notification_enabled)
                                    <span class="badge bg-success">Bật</span>
                                @else
                                    <span class="badge bg-secondary">Tắt</span>
                                @endif
                            </td>
                            <td>{{ $bookmark->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $bookmark->last_read_at ? $bookmark->last_read_at->format('d/m/Y H:i') : 'Chưa đọc' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted">Chưa có người theo dõi</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($bookmarks->hasPages())
            <div class="d-flex justify-content-center mt-2">
                <x-pagination :paginator="$bookmarks" />
            </div>
            @endif
        </div>
    </div>

    @push('info_scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var params = new URLSearchParams(window.location.search);
        var tabEl = null;
        if (params.has('chapter_page')) {
            tabEl = document.querySelector('a[href="#author-chapter-purchases"]');
        } else if (params.has('bookmark_page')) {
            tabEl = document.querySelector('a[href="#author-bookmarks"]');
        } else if (params.has('story_page')) {
            tabEl = document.querySelector('a[href="#author-story-purchases"]');
        }
        if (tabEl && !tabEl.classList.contains('active')) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                new bootstrap.Tab(tabEl).show();
            } else {
                tabEl.click();
            }
        }
        document.querySelectorAll('a[href="#author-story-purchases"], a[href="#author-chapter-purchases"], a[href="#author-bookmarks"]').forEach(function(link) {
            link.addEventListener('click', function() {
                window.location.hash = this.getAttribute('href');
            });
        });
    });
    </script>
    @endpush

@if($story->storySubmits->isNotEmpty())
    <div class="author-form-card mb-4">
        <h6 class="author-form-section-title"><i class="fa-solid fa-history me-2"></i> Lịch sử gửi duyệt</h6>
        <div class="table-responsive">
            <table class="table table-sm author-data-table">
                <thead><tr><th>Lần</th><th>Ngày gửi</th><th>Ghi chú</th><th>Kết quả</th><th>Phản hồi admin</th></tr></thead>
                <tbody>
                    @foreach($story->storySubmits->sortBy('submitted_at') as $sub)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $sub->submitted_at->format('d/m/Y H:i') }}</td>
                        <td>{{ Str::limit($sub->submitted_note ?? '-', 40) }}</td>
                        <td>
                            @if($sub->result == 'pending')<span class="badge bg-warning">Chờ</span>
                            @elseif($sub->result == 'approved')<span class="badge bg-success">Duyệt</span>
                            @else<span class="badge bg-danger">Từ chối</span>@endif
                        </td>
                        <td>{{ Str::limit($sub->admin_note ?? '-', 50) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
