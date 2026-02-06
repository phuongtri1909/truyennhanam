@extends('layouts.information')

@section('info_title', 'Sửa giá hàng loạt: ' . $story->title)
@section('info_section_title', 'Sửa giá chương hàng loạt')
@section('info_section_desc', $story->title)

@section('info_content')
@include('components.toast')
<div class="author-application-form-wrapper author-story-compact">
    <div class="author-form-info-banner">
        <div class="author-form-info-icon"><i class="fa-solid fa-coins"></i></div>
        <div class="author-form-info-content">
            <h6 class="author-form-info-title">Sửa giá chương hàng loạt</h6>
            <p class="author-form-info-text mb-0">Nhập khoảng <strong>từ chương → đến chương</strong> hoặc chọn từng chương bằng checkbox, sau đó chọn <strong>Đặt miễn phí</strong> hoặc <strong>Đặt giá (nấm)</strong>. Áp dụng cho truyện thường có chương set xu.</p>
        </div>
    </div>

    <div class="author-form-card">
        <form method="GET" action="{{ route('author.stories.chapters.bulk-edit-price', $story) }}" class="d-flex flex-wrap gap-2 mb-3 align-items-center">
            <div class="author-input-wrapper" style="min-width: 130px;">
                <span class="author-input-icon"><i class="fa-solid fa-filter"></i></span>
                <select name="status" class="form-select author-form-input border-0">
                    <option value="">- Tất cả trạng thái -</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Hiển thị</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Nháp</option>
                </select>
            </div>
            <button type="submit" class="btn author-form-submit-btn btn-sm px-3"><i class="fa-solid fa-filter me-1"></i> Lọc</button>
        </form>

        <form method="POST" action="{{ route('author.stories.chapters.bulk-update-price', $story) }}" id="bulkPriceForm">
            @csrf
            <div class="mb-3">
                <h6 class="author-form-section-title mb-2">Áp dụng từ chương đến chương</h6>
                <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                    <div class="author-input-wrapper" style="width: 100px;">
                        <span class="author-input-icon"><i class="fa-solid fa-hashtag"></i></span>
                        <input type="number" name="from_chapter" id="fromChapter" class="form-control author-form-input" value="{{ old('from_chapter') }}" min="{{ $minChapter ?? 1 }}" max="{{ $maxChapter ?? 999 }}" placeholder="Từ">
                    </div>
                    <span class="text-muted">→</span>
                    <div class="author-input-wrapper" style="width: 100px;">
                        <span class="author-input-icon"><i class="fa-solid fa-hashtag"></i></span>
                        <input type="number" name="to_chapter" id="toChapter" class="form-control author-form-input" value="{{ old('to_chapter') }}" min="{{ $minChapter ?? 1 }}" max="{{ $maxChapter ?? 999 }}" placeholder="Đến">
                    </div>
                    <span class="text-muted small">({{ $minChapter ?? 0 }}–{{ $maxChapter ?? 0 }})</span>
                </div>
            </div>
            <div class="mb-3 d-flex flex-wrap gap-3 align-items-center">
                <label class="d-flex align-items-center gap-2 mb-0">
                    <input type="radio" name="action" value="free" checked class="form-check-input">
                    <span>Đặt miễn phí</span>
                </label>
                <label class="d-flex align-items-center gap-2 mb-0">
                    <input type="radio" name="action" value="price" class="form-check-input">
                    <span>Đặt giá</span>
                </label>
                <div id="priceInputWrap" class="d-none">
                    <div class="author-input-wrapper d-inline-flex" style="width: 120px;">
                        <span class="author-input-icon"><i class="fa-solid fa-coins"></i></span>
                        <input type="number" name="price" id="priceInput" class="form-control author-form-input" value="0" min="0" placeholder="0">
                    </div>
                    <span class="text-muted small">nấm</span>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-hover align-middle author-data-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="selectAll" class="form-check-input" title="Chọn tất cả (trang này)">
                            </th>
                            <th>STT</th>
                            <th>Tên chương</th>
                            <th>Giá hiện tại</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($chapters as $chapter)
                        <tr>
                            <td>
                                <input type="checkbox" name="chapter_ids[]" value="{{ $chapter->id }}" class="form-check-input chapter-check">
                            </td>
                            <td>Chương {{ $chapter->number }}</td>
                            <td>{{ Str::limit($chapter->title, 40) }}</td>
                            <td>{{ $chapter->is_free ? 'Miễn phí' : $chapter->price . ' nấm' }}</td>
                            <td><span class="badge author-status-tag bg-{{ $chapter->status == 'published' ? 'success' : 'secondary' }}">{{ $chapter->status == 'published' ? 'Hiển thị' : 'Nháp' }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Chưa có chương nào</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($chapters->hasPages())
            <div class="mb-3">
                <x-pagination :paginator="$chapters" />
            </div>
            @endif

            <div class="author-form-submit-wrapper">
                <button type="submit" class="btn author-form-submit-btn btn-sm" id="submitBtn" disabled>
                    <i class="fa-solid fa-check me-1"></i> Áp dụng
                </button>
                <a href="{{ route('author.stories.chapters.index', $story) }}" class="btn btn-outline-secondary btn-sm">Quay lại</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('info_scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('bulkPriceForm');
    var selectAll = document.getElementById('selectAll');
    var chapterChecks = document.querySelectorAll('.chapter-check');
    var priceInputWrap = document.getElementById('priceInputWrap');
    var priceInput = document.getElementById('priceInput');
    var actionRadios = form.querySelectorAll('input[name="action"]');
    var submitBtn = document.getElementById('submitBtn');

    actionRadios.forEach(function(r) {
        r.addEventListener('change', function() {
            if (this.value === 'price') {
                priceInputWrap.classList.remove('d-none');
                priceInput.required = true;
            } else {
                priceInputWrap.classList.add('d-none');
                priceInput.required = false;
                priceInput.value = '0';
            }
        });
    });

    selectAll.addEventListener('change', function() {
        chapterChecks.forEach(function(cb) {
            cb.checked = selectAll.checked;
        });
        updateSubmitState();
    });

    chapterChecks.forEach(function(cb) {
        cb.addEventListener('change', updateSubmitState);
    });

    function updateSubmitState() {
        var checked = document.querySelectorAll('.chapter-check:checked').length;
        var fromVal = document.getElementById('fromChapter').value.trim();
        var toVal = document.getElementById('toChapter').value.trim();
        var hasRange = fromVal !== '' && toVal !== '';
        submitBtn.disabled = checked === 0 && !hasRange;
    }

    document.getElementById('fromChapter').addEventListener('input', updateSubmitState);
    document.getElementById('toChapter').addEventListener('input', updateSubmitState);

    form.addEventListener('submit', function(e) {
        if (form.dataset.bulkConfirmDone === '1') {
            delete form.dataset.bulkConfirmDone;
            return;
        }
        e.preventDefault();
        var checked = document.querySelectorAll('.chapter-check:checked').length;
        var fromVal = document.getElementById('fromChapter').value.trim();
        var toVal = document.getElementById('toChapter').value.trim();
        var hasRange = fromVal !== '' && toVal !== '';
        if (checked === 0 && !hasRange) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'warning', title: 'Chưa chọn chương', text: 'Vui lòng chọn chương hoặc nhập khoảng từ chương đến chương.' });
            } else {
                alert('Vui lòng chọn chương hoặc nhập khoảng từ chương đến chương.');
            }
            return;
        }
        if (hasRange) {
            var fromNum = parseInt(fromVal, 10);
            var toNum = parseInt(toVal, 10);
            if (isNaN(fromNum) || isNaN(toNum) || fromNum > toNum) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'warning', title: 'Khoảng không hợp lệ', text: 'Số chương từ phải nhỏ hơn hoặc bằng số chương đến.' });
                } else {
                    alert('Số chương từ phải nhỏ hơn hoặc bằng số chương đến.');
                }
                return;
            }
        }
        if (document.querySelector('input[name="action"]:checked').value === 'price') {
            var price = parseInt(priceInput.value, 10);
            if (isNaN(price) || price < 0) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'warning', title: 'Giá không hợp lệ', text: 'Vui lòng nhập giá hợp lệ (số nguyên >= 0).' });
                } else {
                    alert('Vui lòng nhập giá hợp lệ (số nguyên >= 0).');
                }
                return;
            }
        }
        var action = document.querySelector('input[name="action"]:checked').value;
        var countText = hasRange ? 'chương ' + fromVal + '–' + toVal : checked + ' chương đã chọn';
        var msg = action === 'free'
            ? 'Đặt miễn phí ' + countText + '?'
            : 'Đặt giá ' + priceInput.value + ' nấm cho ' + countText + '?';
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Xác nhận',
                text: msg,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Áp dụng',
                cancelButtonText: 'Hủy'
            }).then(function(result) {
                if (result.isConfirmed) {
                    form.dataset.bulkConfirmDone = '1';
                    form.submit();
                }
            });
        } else {
            if (confirm(msg)) form.submit();
        }
    });

    updateSubmitState();
});
</script>
@endpush
