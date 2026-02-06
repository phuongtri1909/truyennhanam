@extends('layouts.information')

@section('info_title', 'Donate cám')
@section('info_description', 'Donate cám cho người khác trên ' . request()->getHost())
@section('info_keyword', 'Donate cám, tặng cám, ' . request()->getHost())
@section('info_section_title', 'Donate cám')
@section('info_section_desc', 'Donate cám cho dịch giả hoặc người dùng khác')

@push('styles')
    <style>
        .donate-page .author-form-card { padding: 1rem 1.25rem; margin-bottom: 1rem; }
        .donate-page .author-form-section-title { font-size: 0.95rem; margin-bottom: 0.75rem; padding-bottom: 0.5rem; }
        .donate-page .author-form-group { margin-bottom: 0.9rem; }
        .donate-page .author-form-label { font-size: 0.875rem; margin-bottom: 0.35rem; }
        .donate-page .author-input-wrapper { min-height: 38px; border-radius: 8px; }
        .donate-page .author-input-icon { width: 40px; min-height: 38px; font-size: 0.9rem; }
        .donate-page .author-form-input { padding: 0.4rem 0.75rem 0.4rem 0 !important; font-size: 0.9rem; }
        .donate-page .author-form-textarea { padding: 0.5rem 0.75rem !important; font-size: 0.9rem; min-height: 72px; border-radius: 8px !important; }
        .donate-page .author-form-hint { font-size: 0.8rem; margin-top: 0.25rem; }
        .donate-page .author-form-submit-wrapper { margin-top: 1rem; padding-top: 1rem; }
        .donate-page .author-form-submit-btn { padding: 0.5rem 1.5rem; font-size: 0.9rem; }
        .donate-page .author-form-info-banner { padding: 0.75rem 1rem; margin-bottom: 1rem; gap: 0.75rem; }
        .donate-page .author-form-info-icon { width: 36px; height: 36px; font-size: 1rem; }
        .donate-page .author-form-info-title { font-size: 0.9rem; }
        .donate-page .author-form-info-text { font-size: 0.85rem; }
        .donate-page #donatePreview .payment-info { padding: 0.6rem 0.85rem; }
        .donate-page #donatePreview .transaction-detail-label { font-size: 0.75rem; margin-bottom: 0.15rem; }
        .donate-page #donatePreview .transaction-detail-value { font-size: 0.9rem; }
        .donate-page #donatePreview h6 { font-size: 0.9rem; margin-bottom: 0.5rem; }
        .donate-search-results {
            max-height: 220px;
            overflow-y: auto;
            border: 1px solid rgba(0,0,0,0.08);
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            scrollbar-width: thin;
        }
        .donate-search-results::-webkit-scrollbar { width: 6px; }
        .donate-search-results::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 3px; }
        .donate-search-results::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 3px; }
        .donate-search-results .donate-user-item {
            display: flex;
            align-items: center;
            padding: 0.4rem 0.75rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            text-decoration: none;
            color: inherit;
            transition: background 0.2s;
        }
        .donate-search-results .donate-user-item:last-child { border-bottom: none; }
        .donate-search-results .donate-user-item:hover { background: #e8ebe8; }
        .donate-search-results .donate-user-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            margin-right: 0.5rem;
        }
        .donate-search-results .donate-user-avatar-placeholder {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-right: 0.5rem;
            font-size: 0.7rem;
            color: #6c757d;
        }
        .donate-search-results .donate-user-info { flex: 1; min-width: 0; }
        .donate-search-results .donate-user-name { font-size: 0.875rem; font-weight: 500; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .donate-search-results .donate-user-meta { font-size: 0.75rem; color: #6c757d; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .donate-search-results .donate-user-role { font-size: 0.65rem; padding: 0.1rem 0.4rem; border-radius: 4px; }
        /* Ô đã chọn người nhận gọn */
        .donate-selected-user { padding: 0.4rem 0.75rem; border-radius: 8px; border: 1px solid rgba(0,0,0,0.08); background: #f8f9fa; }
        .donate-selected-user .donate-selected-avatar { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; }
        .donate-selected-user .donate-selected-avatar-ph { width: 32px; height: 32px; border-radius: 50%; background: #dee2e6; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; color: #6c757d; }
        .donate-selected-user .donate-selected-name { font-size: 0.9rem; font-weight: 500; margin: 0; }
        .donate-selected-user .donate-selected-email { font-size: 0.75rem; color: #6c757d; }
        .donate-selected-user .btn { font-size: 0.75rem; padding: 0.2rem 0.5rem; }
    </style>
@endpush

@section('info_content')
    <div class="container-fluid deposit-container donate-page">
        <div class="row">
            <div class="col-12">
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-4">
                    <div class="author-form-info-banner flex-grow-1">
                        <div class="author-form-info-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div>
                            <div class="author-form-info-title">Phí donate</div>
                            <p class="author-form-info-text mb-0">
                                Phí donate hiện tại là <strong>{{ $donateFeePercentage }}%</strong>.
                                Ví dụ: donate <strong>100 cám</strong> thì người nhận nhận <strong>{{ 100 - floor(100 * $donateFeePercentage / 100) }} cám</strong>
                                (phí: {{ floor(100 * $donateFeePercentage / 100) }} cám).
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('user.donate.history') }}" class="btn action-btn-primary btn-sm">
                        <i class="fa-solid fa-list-ul me-1"></i>Lịch sử donate
                    </a>
                </div>

                <div class="author-form-card">
                    <h6 class="author-form-section-title">
                        <i class="fas fa-search me-2"></i>Tìm người nhận & Donate
                    </h6>

                    <form id="donateForm" action="{{ route('user.donate.store') }}" method="POST">
                        @csrf

                        <div class="author-form-group">
                            <label for="userSearch" class="author-form-label">
                                <i class="fas fa-user me-2"></i>Tìm kiếm người nhận (tên hoặc email)
                            </label>
                            <div class="d-flex gap-2 flex-wrap">
                                <div class="author-input-wrapper flex-grow-1" style="min-width: 200px;">
                                    <span class="author-input-icon"><i class="fas fa-search"></i></span>
                                    <input type="text"
                                           class="author-form-input"
                                           id="userSearch"
                                           name="search_query"
                                           placeholder="Nhập tên hoặc email..."
                                           autocomplete="off">
                                </div>
                                <button type="button" class="btn action-btn-primary" id="searchUserBtn">
                                    <i class="fas fa-search me-1"></i>Tìm kiếm
                                </button>
                            </div>
                            <div id="searchResultsWrap" class="mt-2" style="display: none;">
                                <div id="searchResults" class="donate-search-results"></div>
                            </div>
                            <input type="hidden" name="recipient_id" id="recipientId" required>
                            <div id="selectedUser" class="mt-2" style="display: none;">
                                <div class="donate-selected-user d-flex align-items-center">
                                    <div id="selectedUserAvatar" class="me-2"></div>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="donate-selected-name text-dark" id="selectedUserName"></div>
                                        <div class="donate-selected-email" id="selectedUserEmail"></div>
                                    </div>
                                    <button type="button" class="btn btn-sm delete-btn" id="clearSelection">
                                        <i class="fas fa-times me-1"></i>Bỏ chọn
                                    </button>
                                </div>
                            </div>
                            @error('recipient_id')
                                <div class="author-form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="author-form-group">
                            <label for="amount" class="author-form-label">
                                <i class="fas fa-coins me-2"></i>Số cám muốn donate
                            </label>
                            <div class="author-input-wrapper">
                                <span class="author-input-icon"><i class="fas fa-coins"></i></span>
                                <input type="number"
                                       class="author-form-input @error('amount') is-invalid @enderror"
                                       id="amount"
                                       name="amount"
                                       min="1"
                                       value="{{ old('amount') }}"
                                       placeholder="Nhập số cám..."
                                       required>
                            </div>
                            <span class="author-form-hint">
                                Số dư hiện tại: <strong class="color-7">{{ number_format(auth()->user()->coins) }} cám</strong>
                            </span>
                            @error('amount')
                                <div class="author-form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="donatePreview" class="author-form-group" style="display: none;">
                            <div class="payment-info rounded-3 p-3">
                                <h6 class="mb-3 text-dark">
                                    <i class="fas fa-calculator me-2 color-7"></i>Thông tin donate
                                </h6>
                                <div class="row g-2">
                                    <div class="col-6 col-md-3">
                                        <div class="transaction-detail-label">Số cám donate</div>
                                        <div class="transaction-detail-value" id="previewAmount">0</div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="transaction-detail-label">Phí ({{ $donateFeePercentage }}%)</div>
                                        <div class="transaction-detail-value text-danger" id="previewFee">0</div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="transaction-detail-label">Người nhận nhận</div>
                                        <div class="transaction-detail-value text-success" id="previewReceived">0</div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="transaction-detail-label">Số dư sau donate</div>
                                        <div class="transaction-detail-value" id="previewBalance">0</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="author-form-group">
                            <label for="message" class="author-form-label">
                                <i class="fas fa-comment me-2"></i>Lời nhắn (tùy chọn)
                            </label>
                            <textarea class="form-control author-form-textarea @error('message') is-invalid @enderror"
                                      id="message"
                                      name="message"
                                      rows="2"
                                      placeholder="Nhập lời nhắn cho người nhận..."
                                      maxlength="100">{{ old('message') }}</textarea>
                            <span class="author-form-hint"><span id="messageCount">0</span>/100 ký tự</span>
                            @error('message')
                                <div class="author-form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="author-form-submit-wrapper">
                            <button type="submit" class="btn author-form-submit-btn" id="submitBtn" disabled>
                                <i class="fas fa-heart me-2"></i>Xác nhận donate
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('info_scripts')
    <script>
        $(document).ready(function() {
            let searchTimeout;
            const donateFeePercentage = {{ $donateFeePercentage }};
            const currentBalance = {{ auth()->user()->coins }};

            $('#userSearch').on('keyup', function() {
                clearTimeout(searchTimeout);
                const query = $(this).val().trim();
                if (query.length < 2) {
                    $('#searchResultsWrap').hide();
                    $('#searchResults').empty();
                    return;
                }
                searchTimeout = setTimeout(function() { searchUsers(query); }, 500);
            });

            $('#searchUserBtn').on('click', function() {
                const query = $('#userSearch').val().trim();
                if (query.length >= 2) searchUsers(query);
            });

            function searchUsers(query) {
                $.ajax({
                    url: '{{ route("user.donate.search") }}',
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}', query: query },
                    success: function(response) {
                        if (response.success && response.users.length > 0) {
                            var html = '';
                            response.users.forEach(function(user) {
                                var name = (user.name || '').replace(/"/g, '&quot;');
                                var email = (user.email || '').replace(/"/g, '&quot;');
                                var avatar = user.avatar ? '<img src="' + user.avatar + '" class="donate-user-avatar" alt="">' :
                                    '<div class="donate-user-avatar-placeholder"><i class="fas fa-user"></i></div>';
                                html += '<a href="#" class="donate-user-item select-user" data-id="' + user.id + '" data-name="' + name + '" data-email="' + email + '" data-avatar="' + (user.avatar || '') + '" data-role="' + (user.role || '') + '">' +
                                    avatar +
                                    '<div class="donate-user-info">' +
                                    '<div class="donate-user-name">' + (user.name || '') + '</div>' +
                                    '<div class="donate-user-meta">' + (user.email || '') + ' <span class="badge bg-info donate-user-role">' + (user.role_label || '') + '</span></div>' +
                                    '</div></a>';
                            });
                            $('#searchResults').html(html);
                            $('#searchResultsWrap').show();
                        } else {
                            $('#searchResults').html('<div class="p-2 small text-muted text-center"><i class="fas fa-info-circle me-1"></i>Không tìm thấy người dùng nào.</div>');
                            $('#searchResultsWrap').show();
                        }
                    },
                    error: function() {
                        $('#searchResults').html('<div class="p-2 small text-danger text-center"><i class="fas fa-exclamation-triangle me-1"></i>Có lỗi khi tìm kiếm.</div>');
                        $('#searchResultsWrap').show();
                    }
                });
            }

            $(document).on('click', '.select-user', function(e) {
                e.preventDefault();
                var u = $(this).data();
                $('#recipientId').val(u.id);
                $('#selectedUserName').text(u.name);
                $('#selectedUserEmail').text(u.email);
                if (u.avatar) {
                    $('#selectedUserAvatar').html('<img src="' + u.avatar + '" class="donate-selected-avatar" alt="">');
                } else {
                    $('#selectedUserAvatar').html('<div class="donate-selected-avatar-ph"><i class="fas fa-user"></i></div>');
                }
                $('#selectedUser').show();
                $('#searchResultsWrap').hide();
                $('#userSearch').val('');
                updatePreview();
            });

            $('#clearSelection').on('click', function() {
                $('#recipientId').val('');
                $('#selectedUser').hide();
                updatePreview();
            });

            $('#amount').on('input', function() { updatePreview(); });

            function updatePreview() {
                var amount = parseInt($('#amount').val(), 10) || 0;
                var recipientId = $('#recipientId').val();
                if (amount > 0 && recipientId) {
                    var fee = Math.floor(amount * donateFeePercentage / 100);
                    var received = amount - fee;
                    var balanceAfter = currentBalance - amount;
                    $('#previewAmount').text(formatNum(amount) + ' cám');
                    $('#previewFee').text('-' + formatNum(fee) + ' cám');
                    $('#previewReceived').text(formatNum(received) + ' cám');
                    $('#previewBalance').text(formatNum(balanceAfter) + ' cám');
                    $('#donatePreview').show();
                    if (amount > currentBalance) {
                        $('#submitBtn').prop('disabled', true).html('<i class="fas fa-exclamation-triangle me-2"></i>Số dư không đủ');
                    } else {
                        $('#submitBtn').prop('disabled', false).html('<i class="fas fa-heart me-2"></i>Xác nhận donate');
                    }
                } else {
                    $('#donatePreview').hide();
                    $('#submitBtn').prop('disabled', true);
                }
            }

            $('#message').on('input', function() { $('#messageCount').text($(this).val().length); });

            function formatNum(n) { return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ','); }
        });
    </script>
@endpush
