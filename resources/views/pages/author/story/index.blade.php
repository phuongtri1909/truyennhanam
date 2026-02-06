@extends('layouts.information')

@section('info_title', 'Danh sách truyện')
@section('info_section_title', 'Quản lý truyện')
@section('info_section_desc')
    Tổng: {{ $totalStories }} ({{ $publishedStories }} hiển thị / {{ $draftStories }} nháp / {{ $pendingStories }} chờ duyệt / {{ $rejectedStories }} từ chối)
@endsection

@section('info_content')
<div class="author-application-form-wrapper author-story-compact">
    <div class="author-form-card">
        <form method="GET" class="d-flex flex-wrap gap-2 mb-3 align-items-center" id="filterForm">
            <div class="author-input-wrapper" style="min-width: 140px;">
                <span class="author-input-icon"><i class="fa-solid fa-filter"></i></span>
                <select name="status" class="form-select author-form-input border-0">
                    <option value="">- Trạng thái -</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Hiển thị</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Nháp</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                </select>
            </div>
            <div class="author-input-wrapper flex-grow-1" style="min-width: 200px; max-width: 280px;">
                <span class="author-input-icon"><i class="fa-solid fa-search"></i></span>
                <input type="text" class="form-control author-form-input" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm truyện...">
            </div>
            <button type="submit" class="btn author-form-submit-btn btn-sm px-3">
                <i class="fa-solid fa-search me-1"></i> Tìm
            </button>
            <a href="{{ route('author.stories.create') }}" class="btn author-form-submit-btn btn-sm ms-auto">
                <i class="fa-solid fa-plus me-1"></i> Thêm truyện mới
            </a>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle author-data-table">
            <thead>
                <tr>
                    <th>Ảnh</th>
                    <th>Tiêu đề</th>
                    <th>Số chương</th>
                    @if($canPublishZhihu ?? false)
                    <th>Loại</th>
                    @endif
                    <th>Trạng thái</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stories as $story)
                <tr>
                    <td>
                        <img src="{{ Storage::url($story->cover_thumbnail ?? $story->cover) }}" class="rounded" style="width: 50px; height: 70px; object-fit: cover;">
                    </td>
                    <td>
                        <strong>{{ $story->title }}</strong>
                        @if($story->author_name)
                            <div class="text-muted small">{{ $story->author_name }}</div>
                        @endif
                        @if($story->has_combo)
                            <div class="small text-primary"><i class="fa-solid fa-tag me-1"></i>Combo: {{ number_format($story->combo_price) }} nấm</div>
                        @endif
                    </td>
                    <td>{{ $story->chapters_count }}</td>
                    @if($canPublishZhihu ?? false)
                    <td>
                        @if(($story->story_type ?? 'normal') === 'zhihu')
                            <span class="badge author-status-tag bg-info">Zhihu</span>
                        @else
                            <span class="badge author-status-tag bg-secondary">Thường</span>
                        @endif
                    </td>
                    @endif
                    <td>
                        @php
                            $statusBadge = match($story->status) {
                                'published' => ['success', 'Hiển thị'],
                                'draft' => ['secondary', 'Nháp'],
                                'pending' => ['warning', 'Chờ duyệt'],
                                'rejected' => ['danger', 'Từ chối'],
                                default => ['secondary', $story->status]
                            };
                        @endphp
                        <span class="badge author-status-tag bg-{{ $statusBadge[0] }}">{{ $statusBadge[1] }}</span>
                    </td>
                    <td class="text-end">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('author.stories.chapters.index', $story) }}" class="btn btn-sm action-btn-primary" title="Chương">
                                <i class="fa-solid fa-book-open"></i>
                            </a>
                            <a href="{{ route('author.stories.show', $story) }}" class="btn btn-sm action-btn-secondary" title="Xem">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @if(in_array($story->status, ['draft', 'rejected']))
                            <form action="{{ route('author.stories.submit', $story) }}" method="POST" class="d-inline form-submit-confirm form-submit-with-note" data-message="Gửi truyện này cho admin duyệt?">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" title="Gửi duyệt">
                                    <i class="fa-solid fa-paper-plane"></i>
                                </button>
                            </form>
                            @endif
                            <a href="{{ route('author.stories.edit', $story) }}" class="btn btn-sm btn-outline-warning" title="Sửa">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            @if(in_array($story->status, ['draft', 'rejected']))
                            <form action="{{ route('author.stories.destroy', $story) }}" method="POST" class="d-inline form-submit-confirm form-delete-confirm" data-message="Xóa truyện này? Hành động không thể hoàn tác.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm delete-btn" title="Xóa"><i class="fa-solid fa-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ ($canPublishZhihu ?? false) ? 6 : 5 }}">
                        <div class="author-empty-state text-center">
                            <i class="fa-solid fa-book-open empty-icon d-block mb-3"></i>
                            <p class="empty-text mb-3">Chưa có truyện nào</p>
                            <a href="{{ route('author.stories.create') }}" class="btn author-form-submit-btn">
                                <i class="fa-solid fa-plus me-2"></i> Thêm truyện mới
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        @if($stories->hasPages())
        <div class="mt-3 pt-2 border-top">
            <x-pagination :paginator="$stories->withQueryString()" />
        </div>
        @endif
    </div>
</div>
@endsection

@push('info_scripts')
<script>
document.querySelectorAll('#filterForm select').forEach(s => s.addEventListener('change', () => document.getElementById('filterForm').submit()));
</script>
@endpush
