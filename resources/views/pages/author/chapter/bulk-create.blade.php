@extends('layouts.information')

@section('info_title', 'Tạo nhiều chương')
@section('info_section_title', 'Tạo nhiều chương')
@section('info_section_desc', $story->title)

@section('info_content')
<div class="author-application-form-wrapper author-story-compact">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="story-is-zhihu" content="{{ ($story->story_type ?? 'normal') === 'zhihu' ? '1' : '0' }}">
    <div class="author-form-info-banner">
        <div class="author-form-info-icon"><i class="fa-solid fa-lightbulb"></i></div>
        <div class="author-form-info-content">
            <h6 class="author-form-info-title">Tạo nhiều chương</h6>
            <p class="author-form-info-text mb-0">Paste nội dung theo định dạng <strong>"Chương [số]: [tên chương]"</strong>. Hệ thống tự động phân tích và trích xuất các chương. Có thể thiết lập giá, hẹn giờ công bố cho từng chương.</p>
        </div>
    </div>

    <div class="author-form-card">
        <form id="bulkCreateForm" method="POST" action="{{ route('author.stories.chapters.bulk-store', $story) }}">
            @csrf
            <div class="form-group author-form-group mb-3">
                <label class="author-form-label">Nội dung từ file mẫu</label>
                <textarea class="form-control author-form-textarea" id="sampleContent" rows="30" placeholder="Chương 1: Khởi đầu&#10;Nội dung chương 1...&#10;&#10;Chương 2: Tiếp theo&#10;Nội dung chương 2..."></textarea>
            </div>

            <h6 class="author-form-section-title mb-2">Thiết lập chung</h6>
            <div class="row mb-4">
                @if(($story->story_type ?? 'normal') !== 'zhihu')
                <div class="col-md-4">
                    <div class="form-group author-form-group">
                        <label class="author-form-label">Giá mặc định (cám)</label>
                        <div class="author-input-wrapper">
                            <span class="author-input-icon"><i class="fa-solid fa-coins"></i></span>
                            <input type="number" class="form-control author-form-input" id="globalPrice" value="0" min="0">
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-md-4">
                    <div class="form-group author-form-group">
                        <label class="author-form-label">Ngày công bố đầu tiên</label>
                        <div class="author-input-wrapper">
                            <span class="author-input-icon"><i class="fa-solid fa-calendar"></i></span>
                            <input type="datetime-local" class="form-control author-form-input" id="globalPublishedAt">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group author-form-group">
                        <label class="author-form-label">Thời gian giữa các chương</label>
                        <div class="row g-1">
                            <div class="col-6">
                                <input type="number" class="form-control author-form-input" id="globalIntervalValue" value="5" min="1">
                            </div>
                            <div class="col-6">
                                <select class="form-select author-form-input" id="globalIntervalUnit">
                                    <option value="hours">Giờ</option>
                                    <option value="days">Ngày</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="analysisResults" style="display: none;">
                <h6 class="author-form-section-title mb-2">Kết quả phân tích</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered author-data-table">
                        <thead>
                            <tr>
                                <th>Số chương</th>
                                <th>Tên chương</th>
                                @if(($story->story_type ?? 'normal') !== 'zhihu')<th>Giá</th>@endif
                                <th>Ngày công bố</th>
                                <th>Public luôn</th>
                                <th>Thiết lập riêng</th>
                            </tr>
                        </thead>
                        <tbody id="previewTableBody"></tbody>
                    </table>
                </div>
            </div>

            <div class="author-form-submit-wrapper">
                <button type="button" class="btn author-form-submit-btn btn-sm" id="detectChapters">
                    <i class="fa-solid fa-search me-1"></i> Phân tích chương
                </button>
                <button type="submit" class="btn author-form-submit-btn btn-sm" id="createChapters" style="display: none;">
                    <i class="fa-solid fa-plus me-1"></i> Tạo tất cả chương
                </button>
                <a href="{{ route('author.stories.chapters.index', $story) }}" class="btn btn-outline-secondary btn-sm">Quay lại</a>
            </div>
        </form>
    </div>
</div>

<!-- Modal thiết lập riêng -->
<div class="modal fade" id="chapterSettingsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thiết lập Chương <span id="modalChapterNumber"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if(($story->story_type ?? 'normal') !== 'zhihu')
                <div class="mb-3">
                    <label class="author-form-label">Giá riêng (cám)</label>
                    <div class="author-input-wrapper">
                        <span class="author-input-icon"><i class="fa-solid fa-coins"></i></span>
                        <input type="number" class="form-control author-form-input" id="individualPrice" min="0">
                    </div>
                </div>
                @endif
                <div class="mb-3">
                    <label class="form-label">
                        <input type="checkbox" id="useIndividualPublishedAt"> Sử dụng ngày công bố riêng
                    </label>
                    <input type="datetime-local" class="form-control author-form-input mt-1" id="individualPublishedAt" disabled>
                </div>
                <div class="mb-3">
                    <label class="author-form-label">Tên chương riêng</label>
                    <div class="author-input-wrapper">
                        <span class="author-input-icon"><i class="fa-solid fa-heading"></i></span>
                        <input type="text" class="form-control author-form-input" id="individualTitle">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">
                        <input type="checkbox" id="individualPublishNow"> Public ngay lập tức
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn author-form-submit-btn" id="saveIndividualSettings">Lưu</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('info_scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let detectedChapters = [];
    let currentChapterIndex = null;
    const isZhihu = document.querySelector('meta[name="story-is-zhihu"]')?.content === '1';

    fetch('{{ route("author.get-server-time") }}')
        .then(r => r.json())
        .then(data => { document.getElementById('globalPublishedAt').value = data.time || ''; })
        .catch(() => {
            const now = new Date();
            document.getElementById('globalPublishedAt').value = now.toISOString().slice(0, 16);
        });

    function detectChapters() {
        const content = document.getElementById('sampleContent').value.trim();
        if (!content) {
            if (typeof Swal !== 'undefined') Swal.fire('Lỗi', 'Vui lòng nhập nội dung', 'error');
            else alert('Vui lòng nhập nội dung');
            return;
        }
        const chapters = [];
        const lines = content.split('\n');
        let cur = null, parts = [];
        for (let i = 0; i < lines.length; i++) {
            const line = lines[i].trim();
            const m = line.match(/^Chương\s*(\d+)(?:\s*:\s*(.*))?$/);
            if (m) {
                if (cur) chapters.push({ number: cur.num, title: cur.title, content: parts.join('\n').trim() });
                cur = { num: parseInt(m[1]), title: (m[2] || 'Chương ' + m[1]).trim() };
                parts = [];
            } else if (cur) parts.push(line);
        }
        if (cur) chapters.push({ number: cur.num, title: cur.title, content: parts.join('\n').trim() });

        if (chapters.length === 0) {
            if (typeof Swal !== 'undefined') Swal.fire('Lỗi', 'Không tìm thấy chương', 'error');
            else alert('Không tìm thấy chương');
            return;
        }

        fetch('{{ route("author.stories.chapters.check-existing", $story) }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ chapter_numbers: chapters.map(c => c.number) })
        })
        .then(r => r.json())
        .then(data => {
            const existing = data.existing || [];
            detectedChapters = chapters.map(c => ({
                ...c,
                existing: existing.includes(c.number),
                settings: {
                    price: isZhihu ? 0 : (parseInt(document.getElementById('globalPrice')?.value) || 0),
                    published_at: document.getElementById('globalPublishedAt').value,
                    useCustom: false,
                    customPrice: null,
                    customPublishedAt: null,
                    customTitle: null,
                    publishNow: false
                }
            }));
            displayDetectedChapters();
        })
        .catch(() => {
            if (typeof Swal !== 'undefined') Swal.fire('Lỗi', 'Có lỗi khi kiểm tra chương', 'error');
            else alert('Có lỗi xảy ra');
        });
    }

    function formatDateTime(str) {
        if (!str) return '-';
        const d = new Date(str + '+07:00');
        return d.toLocaleString('vi-VN', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Ho_Chi_Minh' });
    }

    function calculatePublishedDates() {
        const base = document.getElementById('globalPublishedAt').value;
        const val = parseInt(document.getElementById('globalIntervalValue').value) || 5;
        const unit = document.getElementById('globalIntervalUnit').value;
        const mult = unit === 'hours' ? 3600000 : 86400000;

        detectedChapters.forEach((ch, i) => {
            if (!ch.settings.publishNow && !ch.settings.useCustom) {
                const d = new Date(base + '+07:00');
                d.setTime(d.getTime() + i * val * mult);
                const y = d.getFullYear(), m = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                const h = String(d.getHours()).padStart(2, '0'), min = String(d.getMinutes()).padStart(2, '0');
                ch.settings.published_at = `${y}-${m}-${day}T${h}:${min}`;
            }
        });
    }

    function displayDetectedChapters() {
        calculatePublishedDates();
        const tbody = document.getElementById('previewTableBody');
        tbody.innerHTML = '';

        detectedChapters.forEach((ch) => {
            const row = document.createElement('tr');
            row.classList.add(ch.existing ? 'table-warning' : 'table-success');
            const priceVal = ch.settings.customPrice !== null ? ch.settings.customPrice : ch.settings.price;
            const dateStr = ch.settings.publishNow ? 'Ngay lập tức' : formatDateTime(ch.settings.customPublishedAt || ch.settings.published_at);
            const priceTd = isZhihu ? '' : `<td>${priceVal.toLocaleString()} cám</td>`;
            row.innerHTML = `
                <td><strong>Chương ${ch.number}</strong> ${ch.existing ? '<span class="badge bg-warning ms-1">Đã tồn tại</span>' : '<span class="badge bg-success ms-1">Mới</span>'}</td>
                <td><small>${(ch.settings.customTitle || ch.title).substring(0, 40)}...</small></td>
                ${priceTd}
                <td>${dateStr}</td>
                <td>${ch.existing ? '-' : `<input type="checkbox" class="form-check-input publish-now-cb" data-num="${ch.number}">`}</td>
                <td>${ch.existing ? '-' : `<button type="button" class="btn btn-sm btn-outline-primary" onclick="openSettings(${ch.number})"><i class="fa-solid fa-cog"></i></button>`}</td>
            `;
            tbody.appendChild(row);
        });

        document.getElementById('analysisResults').style.display = 'block';
        document.getElementById('createChapters').style.display = 'inline-block';

        document.querySelectorAll('.publish-now-cb').forEach(cb => {
            const ch = detectedChapters.find(c => c.number == cb.dataset.num);
            if (ch) cb.checked = ch.settings.publishNow;
            cb.onchange = () => {
                const ch2 = detectedChapters.find(c => c.number == cb.dataset.num);
                if (ch2) ch2.settings.publishNow = cb.checked;
                displayDetectedChapters();
            };
        });
    }

    window.openSettings = function(num) {
        const ch = detectedChapters.find(c => c.number === num);
        if (!ch) return;
        currentChapterIndex = detectedChapters.indexOf(ch);
        document.getElementById('modalChapterNumber').textContent = num;
        const priceEl = document.getElementById('individualPrice');
        if (priceEl) priceEl.value = ch.settings.customPrice ?? ch.settings.price;
        document.getElementById('individualPublishedAt').value = ch.settings.customPublishedAt || ch.settings.published_at;
        document.getElementById('individualTitle').value = ch.settings.customTitle || ch.title;
        document.getElementById('useIndividualPublishedAt').checked = ch.settings.useCustom;
        document.getElementById('individualPublishNow').checked = ch.settings.publishNow;
        document.getElementById('individualPublishedAt').disabled = !ch.settings.useCustom || ch.settings.publishNow;
        new bootstrap.Modal(document.getElementById('chapterSettingsModal')).show();
    };

    document.getElementById('useIndividualPublishedAt').onchange = function() {
        document.getElementById('individualPublishedAt').disabled = !this.checked || document.getElementById('individualPublishNow').checked;
    };
    document.getElementById('individualPublishNow').onchange = function() {
        document.getElementById('individualPublishedAt').disabled = this.checked;
        document.getElementById('useIndividualPublishedAt').disabled = this.checked;
    };

    document.getElementById('saveIndividualSettings').onclick = function() {
        if (currentChapterIndex === null) return;
        const ch = detectedChapters[currentChapterIndex];
        const priceEl = document.getElementById('individualPrice');
        ch.settings.customPrice = priceEl ? (parseInt(priceEl.value) || 0) : 0;
        ch.settings.customTitle = document.getElementById('individualTitle').value.trim() || null;
        ch.settings.useCustom = document.getElementById('useIndividualPublishedAt').checked;
        ch.settings.publishNow = document.getElementById('individualPublishNow').checked;
        ch.settings.customPublishedAt = ch.settings.useCustom && !ch.settings.publishNow ? document.getElementById('individualPublishedAt').value : null;
        bootstrap.Modal.getInstance(document.getElementById('chapterSettingsModal')).hide();
        displayDetectedChapters();
    };

    const updateFromGlobal = function() {
        if (detectedChapters.length) {
            if (!isZhihu) {
                const newPrice = parseInt(document.getElementById('globalPrice')?.value) || 0;
                detectedChapters.forEach(c => {
                    if (!c.settings.useCustom && c.settings.customPrice === null) c.settings.price = newPrice;
                });
            }
            displayDetectedChapters();
        }
    };
    document.getElementById('globalPublishedAt').onchange = document.getElementById('globalIntervalValue').onchange = document.getElementById('globalIntervalUnit').onchange = updateFromGlobal;
    if (!isZhihu) {
        const gp = document.getElementById('globalPrice');
        if (gp) gp.oninput = updateFromGlobal;
    }

    document.getElementById('detectChapters').onclick = detectChapters;

    document.getElementById('bulkCreateForm').onsubmit = function(e) {
        e.preventDefault();
        const newCh = detectedChapters.filter(c => !c.existing);
        if (!newCh.length) {
            if (typeof Swal !== 'undefined') Swal.fire('Lỗi', 'Không có chương nào để tạo', 'error');
            else alert('Không có chương nào để tạo');
            return;
        }

        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('chapters', JSON.stringify(newCh.map(c => ({
            number: c.number,
            title: c.settings.customTitle || c.title,
            content: c.content,
            price: isZhihu ? 0 : (c.settings.customPrice ?? c.settings.price),
            published_at: c.settings.publishNow ? null : (c.settings.customPublishedAt || c.settings.published_at),
            publish_now: c.settings.publishNow
        }))));

        fetch(this.action, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Thành công', `Đã tạo ${data.created_count} chương`, 'success').then(() => {
                        window.location.href = '{{ route("author.stories.chapters.index", $story) }}';
                    });
                } else {
                    alert('Đã tạo ' + data.created_count + ' chương');
                    window.location.href = '{{ route("author.stories.chapters.index", $story) }}';
                }
            } else {
                if (typeof Swal !== 'undefined') Swal.fire('Lỗi', data.message || 'Có lỗi', 'error');
                else alert(data.message || 'Có lỗi');
            }
        })
        .catch(err => {
            if (typeof Swal !== 'undefined') Swal.fire('Lỗi', err.message, 'error');
            else alert('Có lỗi: ' + err.message);
        });
    };
});
</script>
@endpush
