@extends('layouts.information')

@section('info_title', 'Thêm truyện mới')
@section('info_section_title', 'Đăng truyện mới')
@section('info_section_desc', 'Lưu bản nháp hoặc gửi duyệt để admin phê duyệt')

@section('info_content')
    <div class="author-application-form-wrapper author-story-compact">
        <div class="author-form-info-banner">
            <div class="author-form-info-icon">
                <i class="fa-solid fa-lightbulb"></i>
            </div>
            <div class="author-form-info-content">
                <h6 class="author-form-info-title">Lưu ý khi đăng truyện</h6>
                <p class="author-form-info-text mb-0">Lưu bản nháp để tiếp tục chỉnh sửa sau, hoặc gửi duyệt để admin xem
                    xét. Truyện chỉ hiển thị với độc giả sau khi được duyệt.</p>
            </div>
        </div>

        <form id="author-story-form" action="{{ route('author.stories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="author-form-card">
                <h6 class="author-form-section-title">
                    <i class="fa-solid fa-book me-2"></i> Thông tin truyện
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
                                    value="{{ old('title') }}" placeholder="Nhập tiêu đề truyện" required>
                            </div>
                            @error('title')
                                <div class="author-form-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group author-form-group">
                            <label for="description" class="author-form-label">Mô tả <span
                                    class="text-danger">*</span></label>
                            <textarea name="description" id="description" rows="4"
                                class="form-control author-form-textarea @error('description') is-invalid @enderror"
                                placeholder="Giới thiệu ngắn gọn về truyện..." required>{{ old('description') }}</textarea>
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
                                    value="{{ old('author_name', Auth::user()->name) }}" placeholder="Tên hiển thị" required>
                            </div>
                        </div>

                        <div class="form-group author-form-group">
                            <label class="author-form-label">Khi lưu</label>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="form-check">
                                    <input type="radio" name="status" id="status-draft" value="draft" class="form-check-input" {{ old('status', 'draft') == 'draft' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status-draft">Lưu bản nháp</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="status" id="status-pending" value="pending" class="form-check-input" {{ old('status') == 'pending' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status-pending">Gửi duyệt</label>
                                </div>
                            </div>
                            <small class="author-form-hint">Chọn "Gửi duyệt" để admin xem xét và hiển thị truyện</small>
                        </div>
                        <div id="submitted-note-container" class="form-group author-form-group" style="display: {{ old('status') == 'pending' ? 'block' : 'none' }};">
                            <label for="submitted_note" class="author-form-label">Ghi chú khi gửi duyệt</label>
                            <textarea name="submitted_note" id="submitted_note" class="form-control author-form-textarea" rows="2" placeholder="Tùy chọn: thêm ghi chú cho admin...">{{ old('submitted_note') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h6 class="author-form-section-title mt-0 mt-md-3">
                            <i class="fa-solid fa-image me-2"></i> Cài đặt
                        </h6>
                        @if($canPublishZhihu ?? false)
                        <div class="form-group author-form-group">
                            <label class="author-form-label">Loại truyện</label>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="form-check">
                                    <input type="radio" name="story_type" id="story-type-normal" value="normal" class="form-check-input" {{ old('story_type', 'normal') == 'normal' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="story-type-normal">Truyện thường</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="story_type" id="story-type-zhihu" value="zhihu" class="form-check-input" {{ old('story_type') == 'zhihu' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="story-type-zhihu">Truyện Zhihu</label>
                                </div>
                            </div>
                            <small class="author-form-hint">Truyện Zhihu: miễn phí, có quảng cáo ủng hộ khi đọc</small>
                        </div>
                        @endif
                        <div class="form-group author-form-group">
                            <label for="cover" class="author-form-label">Ảnh bìa <span
                                    class="text-danger">*</span></label>
                            <div class="author-cover-upload {{ $errors->has('cover') ? 'border-danger' : '' }}"
                                id="cover-upload-zone">
                                <input type="file" name="cover" id="cover" onchange="previewImage(this)"
                                    accept="image/*" required>
                                <div class="author-cover-upload-placeholder" id="cover-placeholder">
                                    <i class="fa-solid fa-image"></i>
                                    <span>Nhấn để chọn ảnh bìa</span>
                                    <small class="text-muted">JPG, PNG, GIF (tối đa 2MB)</small>
                                </div>
                                <div id="cover-preview" class="author-cover-preview" style="display: none;"></div>
                                <p class="author-cover-change-hint mb-0" id="cover-change-hint" style="display: none;">Nhấn
                                    để đổi ảnh khác</p>
                            </div>
                            @error('cover')
                                <div class="author-form-error mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group author-form-group">
                            <label class="author-form-label">Thể loại <span class="text-danger">*</span></label>
                            <div class="category-tags-container">
                                <div class="selected-categories" id="selected-categories"></div>
                                <select id="category-select" class="category-select">
                                    <option value="">-- Chọn thể loại --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}@if ($category->is_main)
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


                        <div id="combo-section" class="form-group author-form-group" style="{{ ($canPublishZhihu ?? false) ? (old('story_type') == 'zhihu' ? '' : 'display: none;') : '' }}">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="has_combo" class="form-check-input" id="has_combo" {{ old('has_combo') ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_combo">Bán combo (tất cả chương)</label>
                            </div>
                            <div id="combo-pricing-container" style="display: none;">
                                <label for="combo_price" class="author-form-label">Giá combo (nấm)</label>
                                <div class="author-input-wrapper">
                                    <span class="author-input-icon"><i class="fa-solid fa-coins"></i></span>
                                    <input type="number" name="combo_price" id="combo_price"
                                        class="form-control author-form-input" value="{{ old('combo_price', 0) }}"
                                        min="0" placeholder="0">
                                </div>
                            </div>
                        </div>
                        <div class="form-group author-form-group">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_18_plus" class="form-check-input" id="is_18_plus"
                                    {{ old('is_18_plus') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_18_plus">Nội dung 18+</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="author-form-submit-wrapper">
                <button type="submit" class="btn author-form-submit-btn me-2">
                    <i class="fa-solid fa-save me-2"></i> Lưu truyện
                </button>
                <a href="{{ route('author.stories.index') }}" class="btn btn-outline-secondary">Quay lại</a>
                <p class="mt-2 mb-0 text-muted small">Tùy chọn "Lưu bản nháp" hoặc "Gửi duyệt" ở trên sẽ quyết định trạng
                    thái sau khi lưu.</p>
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
            preview.innerHTML = '';
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Ảnh bìa';
                    preview.appendChild(img);
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                    hint.style.display = 'block';
                    zone.classList.add('has-file');
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
                placeholder.style.display = 'flex';
                hint.style.display = 'none';
                zone.classList.remove('has-file');
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
            const opt = document.querySelector(`#category-select option[value="${id}"]`);
            if (opt) opt.disabled = true;
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
                        document.getElementById('combo-pricing-container').style.display = 'none';
                    } else {
                        toggleComboPrice();
                    }
                }
            }
            function toggleComboPrice() {
                var hasCombo = document.getElementById('has_combo');
                if (hasCombo) {
                    document.getElementById('combo-pricing-container').style.display = hasCombo.checked ? 'block' : 'none';
                }
            }
            document.querySelectorAll('input[name="story_type"]').forEach(function(r) {
                r.addEventListener('change', function() { toggleComboSection(); toggleComboPrice(); });
            });
            if (document.getElementById('has_combo')) {
                document.getElementById('has_combo').addEventListener('change', toggleComboPrice);
            }
            toggleComboSection();
            document.querySelectorAll('input[name="status"]').forEach(function(radio) {
                radio.addEventListener('change', function() {
                    document.getElementById('submitted-note-container').style.display = this
                        .value === 'pending' ? 'block' : 'none';
                });
            });
            if (document.querySelector('input[name="status"]:checked')?.value === 'pending') {
                document.getElementById('submitted-note-container').style.display = 'block';
            }
            document.getElementById('author-story-form').addEventListener('submit', function(e) {
                if (document.querySelectorAll('.category-tag').length === 0) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Thiếu thể loại', 'Vui lòng chọn ít nhất một thể loại.', 'warning');
                    } else {
                        alert('Vui lòng chọn ít nhất một thể loại.');
                    }
                    return false;
                }
                var status = document.querySelector('input[name="status"]:checked')?.value || 'draft';
                if (status === 'pending') {
                    e.preventDefault();
                    updateCategoriesInput();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'question',
                            title: 'Xác nhận',
                            text: 'Bạn sẽ gửi truyện cho admin duyệt. Admin sẽ xem xét và phản hồi.',
                            showCancelButton: true,
                            confirmButtonText: 'Đồng ý',
                            cancelButtonText: 'Hủy',
                            confirmButtonColor: 'var(--primary-color-7, #0d6efd)'
                        }).then(function(r) {
                            if (r.isConfirmed) {
                                updateCategoriesInput();
                                document.getElementById('author-story-form').submit();
                            }
                        });
                    } else {
                        updateCategoriesInput();
                        document.getElementById('author-story-form').submit();
                    }
                } else {
                    updateCategoriesInput();
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
