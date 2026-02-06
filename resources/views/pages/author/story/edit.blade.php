@extends('layouts.information')

@section('info_title', 'Chỉnh sửa truyện')
@section('info_section_title', 'Chỉnh sửa: ' . $story->title)
@section('info_section_desc')
    @if ($story->status === 'published')
        Truyện đang hiển thị. Cập nhật "Đã hoàn thành" và "Bán combo" áp dụng ngay, không cần duyệt. Các thay đổi khác cần admin duyệt.
    @else
        Chỉnh sửa thoải mái, chọn "Gửi duyệt" khi sẵn sàng. Đánh dấu hoàn thành chỉ có khi truyện đã được duyệt.
    @endif
@endsection

@section('info_content')
    <div class="author-application-form-wrapper author-story-compact">
        @if ($hasPendingEditRequest ?? false)
            <div class="alert alert-warning mb-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <span><i class="fa-solid fa-clock me-2"></i> Bạn có yêu cầu chỉnh sửa đang chờ admin duyệt{{ $pendingEditRequest && $pendingEditRequest->submitted_at ? ' (gửi ' . $pendingEditRequest->submitted_at->format('d/m/Y H:i') . ')' : '' }}.</span>
                <form action="{{ route('author.stories.edit-requests.withdraw', [$story, $pendingEditRequest]) }}" method="POST" class="d-inline form-submit-confirm" data-message="Rút lại yêu cầu chỉnh sửa? Bạn có thể gửi yêu cầu mới sau.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-warning btn-sm">Rút lại yêu cầu</button>
                </form>
            </div>
        @endif
        <div class="author-form-card mb-3">
            <a href="{{ route('author.stories.chapters.index', $story) }}" class="btn author-form-submit-btn btn-sm">
                <i class="fa-solid fa-book-open me-1"></i> Quản lý chương ({{ $story->chapters()->count() }})
            </a>
        </div>

        <form id="author-story-form" action="{{ route('author.stories.update', $story) }}" method="POST" enctype="multipart/form-data"
            data-original-title="{{ $story->title }}"
            data-original-categories="{{ $story->categories->pluck('id')->sort()->implode(',') }}">
            @csrf
            @method('PUT')
            <div class="author-form-card">
                <h6 class="author-form-section-title">
                    <i class="fa-solid fa-pen me-2"></i> Chỉnh sửa truyện
                </h6>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group author-form-group">
                            <label for="title" class="author-form-label">Tiêu đề <span
                                    class="text-danger">*</span></label>
                            <div class="author-input-wrapper">
                                <span class="author-input-icon"><i class="fa-solid fa-heading"></i></span>
                                <input type="text" name="title" id="title"
                                    class="form-control author-form-input @error('title') is-invalid @enderror"
                                    value="{{ old('title', $story->title) }}" required>
                            </div>
                            @error('title')
                                <div class="author-form-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group author-form-group">
                            <label for="description" class="author-form-label">Mô tả <span
                                    class="text-danger">*</span></label>
                            <textarea name="description" id="description" rows="4"
                                class="form-control author-form-textarea @error('description') is-invalid @enderror" required>{{ old('description', $story->description) }}</textarea>
                            @error('description')
                                <div class="author-form-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group author-form-group">
                            <label for="author_name" class="author-form-label">Tên tác giả hiển thị <span
                                class="text-danger">*</span></label>
                            <div class="author-input-wrapper">
                                <span class="author-input-icon"><i class="fa-solid fa-user-pen"></i></span>
                                <input type="text" name="author_name" id="author_name"
                                    class="form-control author-form-input"
                                    value="{{ old('author_name', $story->author_name ?? Auth::user()->name) }}" required>
                            </div>
                        </div>

                        @if ($story->status !== 'published')
                            <div class="form-group author-form-group">
                                <label class="author-form-label">Trạng thái</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <div class="form-check">
                                        <input type="radio" name="status" id="status-draft" value="draft"
                                            class="form-check-input"
                                            {{ old('status', $story->status) == 'draft' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status-draft">Lưu bản nháp</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="status" id="status-pending" value="pending"
                                            class="form-check-input"
                                            {{ old('status', $story->status) == 'pending' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status-pending">Gửi duyệt</label>
                                    </div>
                                </div>
                            </div>
                            <div id="submitted-note-container" class="form-group author-form-group"
                                style="display: {{ old('status', $story->status) == 'pending' ? 'block' : 'none' }};">
                                <label for="submitted_note" class="author-form-label">Ghi chú khi gửi duyệt</label>
                                <textarea name="submitted_note" id="submitted_note" class="form-control author-form-textarea" rows="2"
                                    placeholder="Tùy chọn: thêm ghi chú cho admin...">{{ old('submitted_note', $story->submitted_note) }}</textarea>
                            </div>
                        @else
                            <div class="form-group author-form-group">
                                <label for="review_note" class="author-form-label">Ghi chú khi gửi yêu cầu chỉnh sửa</label>
                                <textarea name="review_note" id="review_note" class="form-control author-form-textarea" rows="2"
                                    placeholder="Tùy chọn: thêm ghi chú cho admin...">{{ old('review_note') }}</textarea>
                                <small class="author-form-hint">Chỉ cần khi thay đổi nội dung (ngoài trạng thái hoàn
                                    thành)</small>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-4">

                        <div class="form-group author-form-group">
                            <label for="cover" class="author-form-label">Ảnh bìa</label>
                            <div class="author-cover-upload {{ $errors->has('cover') ? 'border-danger' : '' }} {{ $story->cover ? 'has-file' : '' }}"
                                id="cover-upload-zone">
                                <input type="file" name="cover" id="cover" onchange="previewImage(this)"
                                    accept="image/*">
                                <div class="author-cover-upload-placeholder" id="cover-placeholder"
                                    style="{{ $story->cover ? 'display: none;' : '' }}">
                                    <i class="fa-solid fa-image"></i>
                                    <span>Nhấn để chọn ảnh bìa mới</span>
                                    <small class="text-muted">JPG, PNG, GIF (tối đa 2MB)</small>
                                </div>
                                <div id="cover-preview" class="author-cover-preview"
                                    style="{{ $story->cover ? '' : 'display: none;' }}">
                                    @if ($story->cover)
                                        <img src="{{ Storage::url($story->cover_thumbnail ?? $story->cover) }}"
                                            alt="Ảnh bìa">
                                    @endif
                                </div>
                                <p class="author-cover-change-hint mb-0" id="cover-change-hint"
                                    style="{{ $story->cover ? '' : 'display: none;' }}">Nhấn để đổi ảnh khác</p>
                            </div>
                            @error('cover')
                                <div class="author-form-error mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group author-form-group">
                            <label class="author-form-label">Thể loại <span class="text-danger">*</span></label>
                            <div class="category-tags-container">
                                <div class="selected-categories" id="selected-categories">
                                    @foreach ($story->categories as $category)
                                        <span class="category-tag" data-category-id="{{ $category->id }}">
                                            {{ $category->name }} <button type="button"
                                                class="btn-close btn-close-white ms-1"
                                                onclick="removeCategory({{ $category->id }})"></button>
                                        </span>
                                    @endforeach
                                </div>
                                @foreach ($story->categories as $category)
                                    <input type="hidden" name="categories[]" value="{{ $category->id }}">
                                @endforeach
                                <select id="category-select" class="category-select">
                                    <option value="">-- Chọn thể loại --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $story->categories->contains($category) ? 'disabled' : '' }}>
                                            {{ $category->name }}@if ($category->is_main)
                                                ⭐
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('categories')
                                <div class="author-form-error mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        
                        <div class="form-group author-form-group">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_18_plus" class="form-check-input" id="is_18_plus"
                                    {{ $story->is_18_plus ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_18_plus">Nội dung 18+</label>
                            </div>
                        </div>

                        @if($canPublishZhihu ?? false)
                        <div class="form-group author-form-group">
                            <label class="author-form-label">Loại truyện</label>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="form-check">
                                    <input type="radio" name="story_type" id="story-type-normal" value="normal" class="form-check-input" {{ old('story_type', $story->story_type ?? 'normal') == 'normal' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="story-type-normal">Truyện thường</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="story_type" id="story-type-zhihu" value="zhihu" class="form-check-input" {{ old('story_type', $story->story_type ?? 'normal') == 'zhihu' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="story-type-zhihu">Truyện Zhihu</label>
                                </div>
                            </div>
                            <small class="author-form-hint">Truyện Zhihu: miễn phí, có quảng cáo ủng hộ khi đọc</small>
                        </div>
                        <div id="combo-section" class="form-group author-form-group" style="display: {{ old('story_type', $story->story_type ?? 'normal') == 'zhihu' ? 'block' : 'none' }};">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="has_combo" class="form-check-input" id="has_combo"
                                    {{ $story->has_combo ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_combo">Bán combo</label>
                            </div>
                            <div id="combo-pricing-container" style="{{ $story->has_combo ? '' : 'display: none;' }}">
                                <label for="combo_price" class="author-form-label">Giá combo (nấm)</label>
                                <div class="author-input-wrapper">
                                    <span class="author-input-icon"><i class="fa-solid fa-coins"></i></span>
                                    <input type="number" name="combo_price" id="combo_price"
                                        class="form-control author-form-input"
                                        value="{{ old('combo_price', $story->combo_price) }}" min="0"
                                        placeholder="0">
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="form-group author-form-group">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="has_combo" class="form-check-input" id="has_combo"
                                    {{ $story->has_combo ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_combo">Bán combo</label>
                            </div>
                            <div id="combo-pricing-container" class="form-group author-form-group"
                                style="{{ $story->has_combo ? '' : 'display: none;' }}">
                                <label for="combo_price" class="author-form-label">Giá combo (nấm)</label>
                                <div class="author-input-wrapper">
                                    <span class="author-input-icon"><i class="fa-solid fa-coins"></i></span>
                                    <input type="number" name="combo_price" id="combo_price"
                                        class="form-control author-form-input"
                                        value="{{ old('combo_price', $story->combo_price) }}" min="0"
                                        placeholder="0">
                                </div>
                            </div>
                        </div>
                        @endif

                        @if ($story->status === 'published')
                            <div class="form-group author-form-group">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="completed" class="form-check-input" id="completed"
                                        {{ $story->completed ? 'checked' : '' }}>
                                    <label class="form-check-label" for="completed">Đã hoàn thành</label>
                                </div>
                                <small class="author-form-hint">Cập nhật ngay, không cần gửi duyệt. Bán combo cũng áp dụng ngay.</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="author-form-submit-wrapper">
                <button type="submit" class="btn author-form-submit-btn me-2">
                    <i class="fa-solid fa-save me-2"></i> Cập nhật
                </button>
                <a href="{{ route('author.stories.index') }}" class="btn btn-outline-secondary">Quay lại</a>
            </div>
        </form>
    </div>
@endsection

@push('info_scripts')
    <script>
        function previewImage(input) {
            const preview = document.getElementById('cover-preview');
            const placeholder = document.getElementById('cover-placeholder');
            const zone = document.getElementById('cover-upload-zone');
            const hint = document.getElementById('cover-change-hint');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Ảnh bìa">';
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                    hint.style.display = 'block';
                    zone.classList.add('has-file');
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                const currentImg = preview.querySelector('img');
                if (!currentImg || !currentImg.src.includes('storage/')) {
                    preview.innerHTML = '';
                    preview.style.display = 'none';
                    placeholder.style.display = 'flex';
                    hint.style.display = 'none';
                    zone.classList.remove('has-file');
                }
            }
        }

        function addCategory(id, name) {
            if (document.querySelector(`[data-category-id="${id}"]`)) return;
            const tag = document.createElement('span');
            tag.className = 'category-tag';
            tag.setAttribute('data-category-id', id);
            tag.innerHTML =
                `${name} <button type="button" class="btn-close btn-close-white ms-1" onclick="removeCategory(${id})"></button>`;
            document.getElementById('selected-categories').appendChild(tag);
            document.querySelector(`#category-select option[value="${id}"]`).disabled = true;
            updateCategoriesInput();
        }

        function removeCategory(id) {
            document.querySelector(`[data-category-id="${id}"]`)?.remove();
            const opt = document.querySelector(`#category-select option[value="${id}"]`);
            if (opt) opt.disabled = false;
            updateCategoriesInput();
        }

        function updateCategoriesInput() {
            document.querySelectorAll('input[name="categories[]"]').forEach(i => i.remove());
            document.querySelectorAll('.category-tag').forEach(tag => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'categories[]';
                input.value = tag.getAttribute('data-category-id');
                document.querySelector('.category-tags-container').appendChild(input);
            });
        }
        document.addEventListener('DOMContentLoaded', function() {
            const statusRadios = document.querySelectorAll('input[name="status"]');
            statusRadios.forEach(function(r) {
                r.addEventListener('change', function() {
                    const c = document.getElementById('submitted-note-container');
                    if (c) c.style.display = this.value === 'pending' ? 'block' : 'none';
                });
            });
            document.getElementById('category-select').addEventListener('change', function() {
                if (this.value) {
                    addCategory(this.value, this.options[this.selectedIndex].text);
                    this.value = '';
                }
            });
            function toggleComboSection() {
                var storyTypeZhihu = document.getElementById('story-type-zhihu')?.checked;
                var comboSection = document.getElementById('combo-section');
                if (comboSection && document.getElementById('story-type-zhihu')) {
                    comboSection.style.display = storyTypeZhihu ? 'block' : 'none';
                    if (!storyTypeZhihu) {
                        document.getElementById('has_combo').checked = false;
                        document.getElementById('combo_price').value = '0';
                        var pc = document.getElementById('combo-pricing-container');
                        if (pc) pc.style.display = 'none';
                    } else {
                        toggleComboPrice();
                    }
                }
            }
            function toggleComboPrice() {
                var hasCombo = document.getElementById('has_combo');
                var pc = document.getElementById('combo-pricing-container');
                if (hasCombo && pc) pc.style.display = hasCombo.checked ? 'block' : 'none';
            }
            document.querySelectorAll('input[name="story_type"]').forEach(function(r) {
                r.addEventListener('change', toggleComboSection);
            });
            document.getElementById('has_combo').addEventListener('change', toggleComboPrice);
            toggleComboSection();
            const form = document.getElementById('author-story-form');
            const storyStatus = '{{ $story->status }}';
            const isPublished = storyStatus === 'published';
            form.addEventListener('submit', function(e) {
                if (document.querySelectorAll('.category-tag').length === 0) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Thiếu thể loại', 'Vui lòng chọn ít nhất một thể loại.', 'warning');
                    } else {
                        alert('Vui lòng chọn ít nhất một thể loại.');
                    }
                    return false;
                }
                e.preventDefault();
                updateCategoriesInput();
                var needConfirm = false;
                var confirmMsg = '';
                if (isPublished) {
                    var completedEl = document.getElementById('completed');
                    var formEl = form;
                    var origTitle = (formEl.dataset.originalTitle || '').trim();
                    var currTitle = (document.getElementById('title').value || '').trim();
                    var hasOtherChanges = document.getElementById('cover').files.length > 0 || currTitle !==
                        origTitle;
                    if (completedEl && !hasOtherChanges) {
                        var origCategories = (formEl.dataset.originalCategories || '').split(',').filter(
                            Boolean).sort().join(',');
                        var currCategories = Array.from(document.querySelectorAll('.category-tag')).map(t =>
                            t.dataset.categoryId).sort().join(',');
                        if (origCategories !== currCategories) hasOtherChanges = true;
                    }
                    if (hasOtherChanges) {
                        needConfirm = true;
                        confirmMsg =
                            'Truyện đang ở trạng thái đã duyệt. Sau khi bạn cập nhật sẽ gửi yêu cầu chỉnh sửa. Thông tin hiện tại vẫn hiển thị bình thường đến khi admin duyệt thông tin mới.';
                    }
                } else {
                    var selectedStatus = document.querySelector('input[name="status"]:checked')?.value ||
                        'draft';
                    if (selectedStatus === 'pending') {
                        if (storyStatus === 'pending') {
                            needConfirm = true;
                            confirmMsg =
                                'Truyện đang chờ duyệt. Nếu bạn cập nhật sẽ gửi duyệt lại với nội dung mới.';
                        } else {
                            needConfirm = true;
                            confirmMsg = 'Bạn sẽ gửi truyện cho admin duyệt. Admin sẽ xem xét và phản hồi.';
                        }
                    } else if (storyStatus === 'pending') {
                        needConfirm = true;
                        confirmMsg =
                            'Truyện đang chờ duyệt. Bạn lưu nháp thì truyện sẽ về trạng thái nháp.';
                    }
                }
                if (needConfirm && typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'question',
                        title: 'Xác nhận',
                        text: confirmMsg,
                        showCancelButton: true,
                        confirmButtonText: 'Đồng ý',
                        cancelButtonText: 'Hủy',
                        confirmButtonColor: 'var(--primary-color-7, #0d6efd)'
                    }).then(function(r) {
                        if (r.isConfirmed) form.submit();
                    });
                } else {
                    form.submit();
                }
            });
        });
    </script>
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script>
        if (typeof CKEDITOR !== 'undefined') CKEDITOR.replace('description', {
            height: 160
        });
    </script>
@endpush
