@extends('layouts.information')

@section('info_title', 'Chương: ' . $story->title)
@section('info_section_title', 'Quản lý chương')
@section('info_section_desc', $story->title . ' • ' . $totalChapters . ' chương (' . $publishedChapters . ' hiển thị / ' . $draftChapters . ' nháp)')

@section('info_content')
<div class="author-application-form-wrapper author-story-compact">
    <div class="author-form-card">
        <form method="GET" id="filterForm" class="d-flex flex-wrap gap-2 mb-3 align-items-center">
            <div class="author-input-wrapper" style="min-width: 130px;">
                <span class="author-input-icon"><i class="fa-solid fa-filter"></i></span>
                <select name="status" class="form-select author-form-input border-0">
                    <option value="">- Trạng thái -</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Hiển thị</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Nháp</option>
                </select>
            </div>
            <div class="author-input-wrapper" style="min-width: 150px;">
                <span class="author-input-icon"><i class="fa-solid fa-search"></i></span>
                <input type="text" name="search" class="form-control author-form-input" value="{{ request('search') }}" placeholder="Tìm chương...">
            </div>
            <button type="submit" class="btn author-form-submit-btn btn-sm px-3"><i class="fa-solid fa-search me-1"></i> Tìm</button>
            <a href="{{ route('author.stories.chapters.bulk-create', $story) }}" class="btn btn-outline-site btn-sm ms-2">
                <i class="fa-solid fa-plus-circle me-1"></i> Tạo nhiều chương
            </a>
            @if(($story->story_type ?? 'normal') === 'normal')
            <a href="{{ route('author.stories.chapters.bulk-edit-price', $story) }}" class="btn btn-outline-site btn-sm ms-2">
                <i class="fa-solid fa-coins me-1"></i> Sửa giá hàng loạt
            </a>
            @endif
            <a href="{{ route('author.stories.chapters.create', $story) }}" class="btn author-form-submit-btn btn-sm">
                <i class="fa-solid fa-plus me-1"></i> Thêm chương
            </a>
            <a href="{{ route('author.stories.show', $story) }}" class="btn btn-outline-site btn-sm ms-auto">Quay lại</a>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle author-data-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên chương</th>
                    <th>Giá</th>
                    <th>Trạng thái</th>
                    <th>Hẹn giờ</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($chapters as $chapter)
                <tr>
                    <td>Chương {{ $chapter->number }}</td>
                    <td>{{ $chapter->title }}</td>
                    <td>{{ $chapter->is_free ? 'Miễn phí' : $chapter->price . ' cám' }}</td>
                    <td><span class="badge author-status-tag bg-{{ $chapter->status == 'published' ? 'success' : 'secondary' }}">{{ $chapter->status == 'published' ? 'Hiển thị' : 'Nháp' }}</span></td>
                    <td>
                        @if($chapter->scheduled_publish_at && $chapter->status === 'draft')
                            <div class="countdown-timer" data-scheduled-time="{{ $chapter->scheduled_publish_at->toISOString() }}">
                                <span class="text-warning small">
                                    <i class="fa-solid fa-clock me-1"></i>
                                    <span class="countdown-text">Đang tính...</span>
                                </span>
                            </div>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('author.stories.chapters.show', [$story, $chapter]) }}" class="btn btn-sm action-btn-secondary"><i class="fa-solid fa-eye"></i></a>
                        <a href="{{ route('author.stories.chapters.edit', [$story, $chapter]) }}" class="btn btn-sm action-btn-primary"><i class="fa-solid fa-pen"></i></a>
                        <form action="{{ route('author.stories.chapters.destroy', [$story, $chapter]) }}" method="POST" class="d-inline form-submit-confirm form-delete-confirm" data-message="Xóa chương này?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm delete-btn"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="author-empty-state text-center">
                            <i class="fa-solid fa-book-open empty-icon d-block mb-3"></i>
                            <p class="empty-text mb-3">Chưa có chương nào</p>
                            <a href="{{ route('author.stories.chapters.create', $story) }}" class="btn author-form-submit-btn">
                                <i class="fa-solid fa-plus me-2"></i> Thêm chương đầu tiên
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        @if($chapters->hasPages())
        <div class="mt-3 pt-2 border-top">
            <x-pagination :paginator="$chapters->withQueryString()" />
        </div>
        @endif
    </div>
</div>
@endsection

@push('info_scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('filterForm')?.querySelectorAll('select').forEach(s => s.addEventListener('change', () => document.getElementById('filterForm').submit()));

    function updateCountdownTimers() {
        document.querySelectorAll('.countdown-timer').forEach(timer => {
            const scheduledTime = new Date(timer.dataset.scheduledTime);
            const now = new Date();
            const timeDiff = scheduledTime.getTime() - now.getTime();

            if (timeDiff > 0) {
                const days = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);

                let countdownText = '';
                if (days > 0) {
                    countdownText = days + ' ngày ' + hours + 'h ' + minutes + 'm';
                } else if (hours > 0) {
                    countdownText = hours + 'h ' + minutes + 'm ' + seconds + 's';
                } else if (minutes > 0) {
                    countdownText = minutes + 'm ' + seconds + 's';
                } else {
                    countdownText = seconds + 's';
                }

                var txt = timer.querySelector('.countdown-text');
                if (txt) txt.textContent = countdownText;
            } else {
                var txt = timer.querySelector('.countdown-text');
                if (txt) txt.textContent = 'Đã đến giờ';
                var span = timer.querySelector('.text-warning');
                if (span) {
                    span.classList.remove('text-warning');
                    span.classList.add('text-success');
                }
            }
        });
    }

    setInterval(updateCountdownTimers, 1000);
    updateCountdownTimers();
});
</script>
@endpush
