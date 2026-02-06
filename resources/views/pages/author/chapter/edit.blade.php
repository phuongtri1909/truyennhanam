@extends('layouts.information')

@section('info_title', 'Sửa chương ' . $chapter->number)
@section('info_section_title', 'Chỉnh sửa chương ' . $chapter->number)
@section('info_section_desc', $story->title)

@section('info_content')
<div class="author-application-form-wrapper author-story-compact">
    <div class="author-form-card">
    <form action="{{ route('author.stories.chapters.update', [$story, $chapter]) }}" method="POST">
        @csrf
        @method('PUT')
        <h6 class="author-form-section-title">
            <i class="fa-solid fa-pen me-2"></i> Chỉnh sửa chương {{ $chapter->number }}
        </h6>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group author-form-group">
                    <label for="number" class="author-form-label">Số chương</label>
                    <div class="author-input-wrapper">
                        <span class="author-input-icon"><i class="fa-solid fa-hashtag"></i></span>
                        <input type="number" name="number" id="number" class="form-control author-form-input" value="{{ old('number', $chapter->number) }}" required>
                    </div>
                    @error('number')<div class="author-form-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group author-form-group">
                    <label for="status" class="author-form-label">Trạng thái</label>
                    <div class="author-input-wrapper">
                        <span class="author-input-icon"><i class="fa-solid fa-toggle-on"></i></span>
                        <select name="status" id="status" class="form-select author-form-input border-0">
                            <option value="draft" {{ $chapter->status == 'draft' ? 'selected' : '' }}>Nháp</option>
                            <option value="published" {{ $chapter->status == 'published' ? 'selected' : '' }}>Hiển thị</option>
                        </select>
                    </div>
                </div>
            </div>
            <div id="scheduled-wrapper" class="col-12" style="display: {{ $chapter->status == 'draft' ? 'block' : 'none' }};">
                <div class="form-group author-form-group">
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" class="form-check-input" id="need_scheduled" {{ old('scheduled_publish_at', $chapter->scheduled_publish_at) ? 'checked' : '' }}>
                        <label class="form-check-label" for="need_scheduled">Hẹn giờ đăng</label>
                    </div>
                    <div id="scheduled-input-container" style="display: {{ ($chapter->scheduled_publish_at || old('scheduled_publish_at')) ? 'block' : 'none' }};">
                        <div class="author-input-wrapper">
                            <span class="author-input-icon"><i class="fa-solid fa-clock"></i></span>
                            <input type="datetime-local" name="scheduled_publish_at" id="scheduled_publish_at" class="form-control author-form-input"
                                value="{{ old('scheduled_publish_at', $chapter->scheduled_publish_at ? $chapter->scheduled_publish_at->format('Y-m-d\TH:i') : '') }}">
                        </div>
                        <small class="author-form-hint">Chương sẽ tự xuất bản khi đến giờ (cần chạy cron)</small>
                    </div>
                </div>
            </div>
            @if(($story->story_type ?? 'normal') !== 'zhihu')
            <div class="col-12">
                <div class="form-group author-form-group">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="is_free" class="form-check-input" id="is_free" {{ $chapter->is_free ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_free">Chương miễn phí</label>
                    </div>
                </div>
            </div>
            <div class="col-12" id="price-container" style="{{ $chapter->is_free ? 'display: none;' : '' }}">
                <div class="form-group author-form-group">
                    <label for="price" class="author-form-label">Giá (cám)</label>
                    <div class="author-input-wrapper">
                        <span class="author-input-icon"><i class="fa-solid fa-coins"></i></span>
                        <input type="number" name="price" id="price" class="form-control author-form-input" value="{{ old('price', $chapter->price) }}" min="0">
                    </div>
                </div>
            </div>
            @else
            <input type="hidden" name="is_free" value="1">
            <input type="hidden" name="price" value="0">
            @endif
            @if(($story->story_type ?? 'normal') !== 'zhihu')
            <div id="password-section" class="col-12" style="display: {{ $chapter->is_free ? 'block' : 'none' }};">
                <div class="form-group author-form-group">
                    <label class="author-form-label">Chương có mật khẩu không?</label>
                    <div class="d-flex flex-wrap gap-3">
                        <div class="form-check">
                            <input type="radio" name="has_password" id="has_password_no" value="0" class="form-check-input" {{ !$chapter->hasPassword() ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_password_no"><i class="fa-solid fa-lock-open text-success me-1"></i> Không có mật khẩu</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="has_password" id="has_password_yes" value="1" class="form-check-input" {{ $chapter->hasPassword() ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_password_yes"><i class="fa-solid fa-lock text-warning me-1"></i> Có mật khẩu</label>
                        </div>
                    </div>
                </div>
                <div id="password-fields" style="display: {{ $chapter->hasPassword() ? 'block' : 'none' }};">
                    <div class="form-group author-form-group">
                        <label for="chapter_password" class="author-form-label">Mật khẩu chương <span class="text-danger">*</span></label>
                        <div class="author-input-wrapper position-relative">
                            <span class="author-input-icon"><i class="fa-solid fa-key"></i></span>
                            <input type="password" name="chapter_password" id="chapter_password" class="form-control author-form-input" value="{{ old('chapter_password', $chapter->getDecryptedPassword()) }}" placeholder="Nhập mật khẩu" autocomplete="off">
                            <button type="button" class="btn btn-link password-toggle-btn position-absolute end-0 top-50 translate-middle-y me-2 p-0 text-secondary" id="togglePassword" title="Hiện/ẩn mật khẩu" aria-label="Hiện mật khẩu"><i class="fa-regular fa-eye" id="togglePasswordIcon"></i></button>
                        </div>
                        <small class="author-form-hint">Để trống nếu không đổi mật khẩu (khi đã có mật khẩu)</small>
                        @error('chapter_password')<div class="author-form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group author-form-group">
                        <label for="password_hint" class="author-form-label">Gợi ý mật khẩu <span class="text-danger">*</span></label>
                        <textarea name="password_hint" id="password_hint" class="form-control author-form-textarea" rows="2" placeholder="Ví dụ: Nhập phép tính 1 + 1 = ? hoặc hướng dẫn tìm mật khẩu...">{{ old('password_hint', $chapter->password_hint) }}</textarea>
                        <small class="author-form-hint">Hướng dẫn người đọc cách lấy mật khẩu để xem chương</small>
                        @error('password_hint')<div class="author-form-error">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            @endif
            <div class="col-12">
                <div class="form-group author-form-group">
                    <label for="title" class="author-form-label">Tên chương <span class="text-danger">*</span></label>
                    <div class="author-input-wrapper">
                        <span class="author-input-icon"><i class="fa-solid fa-heading"></i></span>
                        <input type="text" name="title" id="title" class="form-control author-form-input" value="{{ old('title', $chapter->title) }}" required>
                    @error('title')<div class="author-form-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-12">
                <div class="form-group author-form-group">
                    <label for="content" class="author-form-label">Nội dung <span class="text-danger">*</span></label>
                    <textarea name="content" id="content" class="form-control author-form-textarea" rows="36" required>{{ old('content', $chapter->content) }}</textarea>
                    @error('content')<div class="author-form-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    @if($prevChapter ?? null)
                        <a href="{{ route('author.stories.chapters.edit', [$story, $prevChapter]) }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-chevron-left me-1"></i> Chương trước ({{ $prevChapter->number }})
                        </a>
                    @endif
                    @if($nextChapter ?? null)
                        <a href="{{ route('author.stories.chapters.edit', [$story, $nextChapter]) }}" class="btn btn-outline-secondary">
                            Chương sau ({{ $nextChapter->number }}) <i class="fa-solid fa-chevron-right ms-1"></i>
                        </a>
                    @endif
                </div>
                <div class="author-form-submit-wrapper">
                    <button type="submit" class="btn author-form-submit-btn me-2">
                        <i class="fa-solid fa-save me-2"></i> Cập nhật
                    </button>
                    <a href="{{ route('author.stories.chapters.index', $story) }}" class="btn btn-outline-secondary">Quay lại</a>
                </div>
            </div>
        </div>
    </form>
    </div>
</div>
@endsection

@push('info_scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusEl = document.getElementById('status');
    const wrapper = document.getElementById('scheduled-wrapper');
    const needScheduled = document.getElementById('need_scheduled');
    const inputContainer = document.getElementById('scheduled-input-container');
    const scheduledInput = document.getElementById('scheduled_publish_at');

    function updateScheduledVisibility() {
        if (statusEl.value === 'draft') {
            wrapper.style.display = 'block';
            inputContainer.style.display = needScheduled.checked ? 'block' : 'none';
            if (!needScheduled.checked) scheduledInput.value = '';
        } else {
            wrapper.style.display = 'none';
            needScheduled.checked = false;
            scheduledInput.value = '';
        }
    }

    statusEl.addEventListener('change', updateScheduledVisibility);
    needScheduled.addEventListener('change', updateScheduledVisibility);
    updateScheduledVisibility();

    if (document.getElementById('is_free')) {
        document.getElementById('is_free').addEventListener('change', function() {
            document.getElementById('price-container').style.display = this.checked ? 'none' : 'block';
            if (this.checked) document.getElementById('price').value = '0';
            togglePasswordSection();
        });
    }
    function togglePasswordSection() {
        var isFree = document.getElementById('is_free');
        var section = document.getElementById('password-section');
        if (section && isFree) {
            section.style.display = isFree.checked ? 'block' : 'none';
            if (!isFree.checked) {
                document.getElementById('has_password_no').checked = true;
                document.getElementById('password-fields').style.display = 'none';
            } else {
                togglePasswordFields();
            }
        }
    }
    function togglePasswordFields() {
        var hasYes = document.getElementById('has_password_yes');
        var fields = document.getElementById('password-fields');
        if (fields && hasYes) {
            fields.style.display = hasYes.checked ? 'block' : 'none';
            if (!hasYes.checked) {
                document.getElementById('chapter_password').value = '';
                document.getElementById('password_hint').value = '';
                document.getElementById('chapter_password').removeAttribute('required');
                document.getElementById('password_hint').removeAttribute('required');
            } else {
                document.getElementById('password_hint').setAttribute('required', 'required');
            }
        }
    }
    if (document.getElementById('has_password_yes')) {
        document.querySelectorAll('input[name="has_password"]').forEach(function(r) {
            r.addEventListener('change', togglePasswordFields);
        });
    }
    if (document.getElementById('togglePassword')) {
        document.getElementById('togglePassword').addEventListener('click', function() {
            var inp = document.getElementById('chapter_password');
            var icon = document.getElementById('togglePasswordIcon');
            if (!icon || !inp) return;
            if (inp.type === 'password') {
                inp.type = 'text';
                icon.className = 'fa-regular fa-eye-slash';
                this.setAttribute('aria-label', 'Ẩn mật khẩu');
            } else {
                inp.type = 'password';
                icon.className = 'fa-regular fa-eye';
                this.setAttribute('aria-label', 'Hiện mật khẩu');
            }
        });
    }
    togglePasswordSection();
    document.querySelector('form').addEventListener('submit', function(e) {
        var hasYes = document.getElementById('has_password_yes');
        if (hasYes && hasYes.checked) {
            var hint = document.getElementById('password_hint');
            if (!hint.value.trim()) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') Swal.fire('Thiếu gợi ý', 'Gợi ý mật khẩu là bắt buộc khi đặt mật khẩu chương.', 'warning');
                else alert('Gợi ý mật khẩu là bắt buộc khi đặt mật khẩu chương.');
                return false;
            }
        }
    });
});
</script>
@endpush
