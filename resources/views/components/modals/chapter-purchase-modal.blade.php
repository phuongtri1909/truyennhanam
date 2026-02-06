<!-- Purchase Modal -->
<div class="modal fade" id="purchaseModal" tabindex="-1" aria-labelledby="purchaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg purchase-modal-simple">
            <div class="modal-header bg-light border-0 text-center">
                <div class="w-100">
                    <h5 class="modal-title fw-bold color-7 mb-0" id="purchaseModalLabel">Xác nhận mua chương</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 purchase-modal-body">
                {{-- Layout giống hình 3: 2 option với dashed "hoặc", content rút gọn --}}
                <div class="purchase-options-block">
                    {{-- Option 1: Mua chương (hoặc mua trọn bộ khi type=story) --}}
                    <div class="purchase-option" id="option-chapter-block">
                        <p class="purchase-option-main mb-1 fs-5 fw-semibold">
                            Cần <span class="price-highlight" id="option-chapter-price"></span> để mở khoá chương này!
                        </p>
                        <p class="purchase-option-sub text-muted small mb-0 color-3" id="option-chapter-sub"></p>
                    </div>

                    {{-- Divider: hoặc --}}
                    <div class="purchase-divider" id="purchase-divider">
                        <span class="divider-line"></span>
                        <span class="divider-text">hoặc</span>
                        <span class="divider-line"></span>
                    </div>

                    {{-- Option 2: Mua trọn bộ --}}
                    <div class="purchase-option" id="option-combo-block">
                        <p class="purchase-option-main mb-1 fw-semibold fs-5">
                            Cần <span class="price-highlight" id="option-combo-price"></span> để mở khoá truyện này!
                        </p>
                        <p class="purchase-option-sub text-muted small mb-0 color-3" id="option-combo-sub"></p>
                    </div>
                </div>

                <div id="insufficient-balance" class="alert alert-warning mt-3 d-none small">
                    <i class="fas fa-exclamation-triangle me-2"></i> Bạn không đủ Nấm. <a href="{{ route('user.bank.auto.deposit') }}" class="alert-link">Nạp Nấm ngay</a>
                </div>
            </div>
            <form id="purchase-form" method="POST">
                @csrf
                <input type="hidden" id="purchase-type" name="purchase_type" value="chapter">
                <input type="hidden" id="purchase-item-id" name="chapter_id" value="">
            </form>
            <div class="modal-footer border-0 pt-0 justify-content-center" id="modal-footer">
                <button type="button" class="btn bg-1 fw-bold text-dark px-4 btn-lg rounded-4 " id="confirm-purchase-btn">
                    <span id="purchase-item-icon"></span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .purchase-modal-simple .modal-body {
            background: #faf8f5;
        }

        .purchase-options-block {
            text-align: center;
        }

        .purchase-option-main {
            font-size: 1rem;
            color: #333;
        }

        .purchase-option-main .price-highlight {
            color: var(--primary-color-1);
            font-weight: 700;
        }

        .purchase-option-sub {
            font-size: 1rem;
        }

        .purchase-option-sub .sub-highlight {
            color: #5a9a9a;
            font-weight: 600;
        }

        .purchase-option-sub .discount-highlight {
            color: #dc3545;
            font-weight: 700;
        }

        .purchase-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin: 1rem 0;
        }

        .divider-line {
            flex: 1;
            height: 0;
            border-top: 3px dashed var(--primary-color-3);
        }

        .divider-text {
            font-size: 1.5rem;
            color: var(--primary-color-3);
            font-weight: 500;
        }

        .purchase-modal-footer .btn.bg-7 {
            background: #7a8c6d;
            border-color: #6b7c5f;
        }

        /* Dark mode */
        body.dark-mode .purchase-modal-simple .modal-body {
            background: #2d2d2d;
        }

        body.dark-mode .purchase-option-main {
            color: #e0e0e0;
        }

        body.dark-mode .purchase-option-sub {
            color: #999 !important;
        }

        body.dark-mode .divider-line {
            border:3px dashed var(--primary-color-3);
        }

        body.dark-mode .divider-text {
            color: #ccc;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.userCoins = {{ auth()->check() ? auth()->user()->coins : 0 }};

        function showPurchaseModal(type, id, title, price, storyId = null, storyTitle = null, comboPrice = null, totalChapterPrice = null, totalChapters = null, discountPercent = null) {
            const modalTitle = document.getElementById('purchaseModalLabel');
            const optionChapterBlock = document.getElementById('option-chapter-block');
            const optionComboBlock = document.getElementById('option-combo-block');
            const purchaseDivider = document.getElementById('purchase-divider');
            const optionChapterPrice = document.getElementById('option-chapter-price');
            const optionChapterSub = document.getElementById('option-chapter-sub');
            const optionComboPrice = document.getElementById('option-combo-price');
            const optionComboSub = document.getElementById('option-combo-sub');
            const itemId = document.getElementById('purchase-item-id');
            const purchaseType = document.getElementById('purchase-type');
            const purchaseForm = document.getElementById('purchase-form');
            const insufficientBalance = document.getElementById('insufficient-balance');
            const confirmBtn = document.getElementById('confirm-purchase-btn');
            const itemIcon = document.getElementById('purchase-item-icon');
            const modalFooter = document.getElementById('modal-footer');

            const existingComboBtn = document.getElementById('purchase-combo-btn');
            if (existingComboBtn) existingComboBtn.remove();

            const fmt = (n) => new Intl.NumberFormat().format(n);

            if (type === 'chapter') {
                modalTitle.textContent = 'Xác nhận mua chương';
                purchaseForm.action = "{{ route('purchase.chapter') }}";
                itemId.name = 'chapter_id';
                itemId.value = id;

                // Option 1: Mua chương
                optionChapterBlock.style.display = 'block';
                optionChapterPrice.textContent = fmt(price) + ' Nấm';

                if (totalChapterPrice && totalChapters && Number(totalChapters) > 0) {
                    optionChapterSub.innerHTML = '(Tương đương <span class="sub-highlight">' + fmt(totalChapterPrice) + ' Nấm</span> cho <span class="sub-highlight">' + fmt(totalChapters) + ' chương</span>)';
                    optionChapterSub.style.display = 'block';
                } else {
                    optionChapterSub.style.display = 'none';
                }

                const hasCombo = storyId && comboPrice && Number(comboPrice) > 0 && totalChapterPrice && Number(totalChapterPrice) > 0 && Number(comboPrice) < Number(totalChapterPrice);
                if (hasCombo) {
                    const discountPercent = Math.round(((Number(totalChapterPrice) - Number(comboPrice)) / Number(totalChapterPrice)) * 100);
                    purchaseDivider.style.display = 'flex';
                    optionComboBlock.style.display = 'block';
                    optionComboPrice.textContent = fmt(comboPrice) + ' Nấm';
                    optionComboSub.innerHTML = '(<span class="discount-highlight">Rẻ hơn ' + discountPercent + '%</span> so với mua lẻ từng chương)';
                    optionComboSub.style.display = 'block';

                    const comboBtn = document.createElement('button');
                    comboBtn.type = 'button';
                    comboBtn.className = 'btn bg-1 fw-bold text-dark px-4 btn-lg rounded-4 me-2';
                    comboBtn.id = 'purchase-combo-btn';
                    comboBtn.innerHTML = 'Mua trọn bộ <span class="badge bg-light text-danger ms-1">-' + discountPercent + '%</span>';
                    comboBtn.setAttribute('data-story-id', storyId);
                    comboBtn.setAttribute('data-story-title', storyTitle || '');
                    comboBtn.setAttribute('data-combo-price', comboPrice);
                    comboBtn.setAttribute('data-total-chapter-price', totalChapterPrice || 0);
                    comboBtn.setAttribute('data-total-chapters', totalChapters || 0);
                    comboBtn.addEventListener('click', function() {
                        try { bootstrap.Modal.getInstance(document.getElementById('purchaseModal')).hide(); } catch (e) {}
                        const tp = this.getAttribute('data-total-chapter-price');
                        const tc = this.getAttribute('data-total-chapters');
                        setTimeout(() => {
                            showPurchaseModal('story', storyId, storyTitle, comboPrice, null, null, null, tp ? parseInt(tp) : null, tc ? parseInt(tc) : null);
                        }, 300);
                    });
                    modalFooter.insertBefore(comboBtn, confirmBtn);
                } else {
                    purchaseDivider.style.display = 'none';
                    optionComboBlock.style.display = 'none';
                }

                itemIcon.innerHTML = 'Mua chương';
                purchaseType.value = 'chapter';
            } else if (type === 'story') {
                if (document.getElementById('purchase-combo-btn')) document.getElementById('purchase-combo-btn').remove();
                modalTitle.textContent = 'Xác nhận mua trọn bộ';
                purchaseForm.action = "{{ route('purchase.story.combo') }}";
                itemId.name = 'story_id';
                itemId.value = id;

                optionChapterBlock.style.display = 'none';
                purchaseDivider.style.display = 'none';
                optionComboBlock.style.display = 'block';
                optionComboPrice.textContent = fmt(price) + ' Nấm';
                const dp = discountPercent ?? (totalChapterPrice && Number(totalChapterPrice) > 0 && Number(price) < Number(totalChapterPrice)
                    ? Math.round(((Number(totalChapterPrice) - Number(price)) / Number(totalChapterPrice)) * 100) : null);
                optionComboSub.innerHTML = dp ? '(<span class="discount-highlight">Rẻ hơn ' + dp + '%</span> so với mua lẻ từng chương)' : '(Rẻ hơn so với mua lẻ từng chương)';
                optionComboSub.style.display = 'block';

                itemIcon.innerHTML = 'Mua truyện';
                purchaseType.value = 'story';
            }

            if (window.userCoins < (type === 'chapter' ? price : price)) {
                insufficientBalance.classList.remove('d-none');
                confirmBtn.disabled = true;
            } else {
                insufficientBalance.classList.add('d-none');
                confirmBtn.disabled = false;
            }

            new bootstrap.Modal(document.getElementById('purchaseModal')).show();
        }

        document.getElementById('confirm-purchase-btn').addEventListener('click', function() {
            const purchaseForm = document.getElementById('purchase-form');
            const purchaseType = document.getElementById('purchase-type').value;
            const itemId = document.getElementById('purchase-item-id').value;
            if (!itemId) return;

            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
            const formData = new FormData(purchaseForm);
            const endpoint = purchaseType === 'chapter' ? "{{ route('purchase.chapter') }}" : "{{ route('purchase.story.combo') }}";

            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(r => r.ok ? r.json() : Promise.reject(new Error('Network error')))
            .then(data => {
                try { bootstrap.Modal.getInstance(document.getElementById('purchaseModal')).hide(); } catch (e) {}
                if (data.success) {
                    Swal.fire({
                        title: 'Thành công!',
                        text: data.message || 'Mua thành công!',
                        icon: 'success',
                        confirmButtonText: 'Đọc ngay',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        if (data.redirect) window.location.href = data.redirect;
                        else window.location.reload();
                    });
                } else {
                    Swal.fire({ title: 'Lỗi', text: data.message || 'Có lỗi xảy ra.', icon: 'error', confirmButtonText: 'Đóng' });
                }
            })
            .catch(() => {
                try { bootstrap.Modal.getInstance(document.getElementById('purchaseModal')).hide(); } catch (e) {}
                Swal.fire({ title: 'Lỗi', text: 'Không kết nối được máy chủ. Vui lòng thử lại.', icon: 'error', confirmButtonText: 'Đóng' });
            })
            .finally(() => {
                const btn = document.getElementById('confirm-purchase-btn');
                btn.disabled = false;
                btn.innerHTML = document.getElementById('purchase-item-icon').innerHTML;
            });
        });
    </script>
@endpush
