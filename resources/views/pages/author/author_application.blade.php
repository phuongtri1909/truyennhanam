@extends('layouts.information')

@section('info_title', 'Đăng ký làm tác giả')
@section('info_description', 'Đăng ký làm tác giả để đăng truyện trên Pink Novel')
@section('info_keyword', 'đăng ký tác giả, tác giả pink novel, sáng tác truyện')

@section('info_section_title', 'Đăng ký làm tác giả')
@section('info_section_desc', 'Hãy điền đầy đủ thông tin để đăng ký trở thành tác giả trên Pink Novel')

@section('info_content')
    @if (isset($application))
        <div class="author-app-status-wrapper">
            <div class="author-app-status-card author-app-status-{{ $application->status }}">
                <div class="author-app-status-header">
                    <div class="author-app-status-icon">
                        @if ($application->isPending())
                            <i class="fa-solid fa-clock"></i>
                        @elseif ($application->isApproved())
                            <i class="fa-solid fa-check-circle"></i>
                        @else
                            <i class="fa-solid fa-times-circle"></i>
                        @endif
                    </div>
                    <div class="author-app-status-title">
                        @if ($application->isPending())
                            Đơn đăng ký đang được xem xét
                        @elseif ($application->isApproved())
                            Đơn đăng ký đã được chấp nhận
                        @else
                            Đơn đăng ký đã bị từ chối
                        @endif
                    </div>
                </div>

                <div class="author-app-status-body">
                    <div class="author-app-meta">
                        <span><i class="fa-regular fa-calendar me-2"></i>Gửi đơn: {{ $application->submitted_at->format('d/m/Y H:i') }}</span>
                    </div>

                    @if ($application->isApproved() || $application->isRejected())
                        <div class="author-app-meta">
                            <span><i class="fa-regular fa-calendar-check me-2"></i>Xét duyệt: {{ $application->reviewed_at->format('d/m/Y H:i') }}</span>
                        </div>

                        @if ($application->admin_note)
                            <div class="author-app-admin-note">
                                <h6 class="author-app-admin-note-title"><i class="fa-solid fa-comment-dots me-2"></i>Phản hồi từ quản trị viên</h6>
                                <p class="mb-0">{{ $application->admin_note }}</p>
                            </div>
                        @endif
                    @endif

                    @if ($application->isApproved())
                        <div class="author-app-actions mt-4">
                            <a href="{{ route('author.index') }}" class="btn author-form-submit-btn">
                                <i class="fa-solid fa-pen-to-square me-2"></i> Đi đến khu vực tác giả
                            </a>
                        </div>
                    @elseif ($application->isRejected())
                        <div class="author-app-reject-content">
                            <p class="author-app-reject-text">Bạn có thể gửi lại đơn đăng ký sau khi đã khắc phục các vấn đề được nêu trong phản hồi.</p>
                            <button class="btn author-form-submit-btn" id="showNewApplicationForm">
                                <i class="fa-solid fa-paper-plane me-2"></i> Gửi đơn đăng ký mới
                            </button>
                        </div>

                        <div class="d-none mt-4" id="newApplicationForm">
                            @include('pages.author.application_form')
                        </div>
                    @else
                        <div class="author-app-pending-info">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            <span>Đơn đăng ký đang được xem xét. Bạn sẽ nhận phản hồi trong vòng 24-48 giờ.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        @include('pages.author.application_form')
    @endif
@endsection

@push('info_scripts')
<script>
    $(document).ready(function() {
        // Character counter for introduction
        $('#introduction').on('input', function() {
            const maxLength = 1000;
            const minLength = 50;
            const currentLength = $(this).val().length;
            const $counter = $('#charCounter');
            
            $counter.text(`${currentLength}/${maxLength}`);
            
            if (currentLength < minLength) {
                $counter.removeClass('author-char-warning author-char-danger').addClass('author-char-danger');
            } else if (currentLength > maxLength * 0.8) {
                $counter.removeClass('author-char-warning author-char-danger').addClass('author-char-warning');
            } else {
                $counter.removeClass('author-char-warning author-char-danger');
            }
        });
        
        // Show new application form button
        $('#showNewApplicationForm').on('click', function() {
            $('#newApplicationForm').removeClass('d-none').addClass('author-form-reveal');
            $(this).addClass('d-none');
        });
        
        // Form validation for URLs
        function isValidUrl(url) {
            try {
                new URL(url);
                return true;
            } catch (e) {
                return false;
            }
        }
        
        $('.validate-url').on('blur', function() {
            const url = $(this).val().trim();
            const $group = $(this).closest('.author-form-group');
            if (url && !isValidUrl(url)) {
                $(this).addClass('is-invalid');
                let $err = $group.find('.author-form-client-error');
                if (!$err.length) $err = $('<div class="author-form-error author-form-client-error"></div>').appendTo($group);
                $err.text('URL không hợp lệ. Vui lòng nhập đúng định dạng (https://example.com)').show();
            } else {
                $(this).removeClass('is-invalid');
                $group.find('.author-form-client-error').hide();
            }
        });
    });
</script>
@endpush 