@extends('admin.layouts.app')

@section('title', 'Chi tiết đơn đăng ký tác giả #' . $application->id)

@push('styles-admin')
<style>
    @media (max-width: 768px) {
        .row .col-md-5, .row .col-md-7 { max-width: 100%; flex: 0 0 100%; }
    }
</style>
@endpush

@section('content-auth')
<div class="row">
    <div class="col-12">
        <div class="card mb-0 mb-md-4">
            <div class="card-header pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Chi tiết đơn đăng ký #{{ $application->id }}</h5>
                    <div>
                        <a href="{{ route('admin.author-applications.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="d-flex align-items-center mt-2">
                    <span class="badge badge-sm bg-gradient-{{ $application->status == 'pending' ? 'warning' : ($application->status == 'approved' ? 'success' : 'danger') }} me-2">
                        {{ $application->status == 'pending' ? 'Chờ duyệt' : ($application->status == 'approved' ? 'Đã duyệt' : 'Từ chối') }}
                    </span>
                    <span class="text-sm text-secondary">
                        Gửi: {{ $application->submitted_at->format('d/m/Y H:i') }}
                        @if ($application->reviewed_at)
                            | Xét duyệt: {{ $application->reviewed_at->format('d/m/Y H:i') }}
                        @endif
                    </span>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="mb-4">
                            <h6 class="text-uppercase text-xs font-weight-bolder opacity-6 mb-3">
                                <i class="fas fa-user me-2"></i> Thông tin người dùng
                            </h6>
                            <div class="card">
                                <div class="card-body pt-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-sm text-secondary">Tên:</span>
                                        <span class="text-sm fw-bold">{{ $application->user->name }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-sm text-secondary">Email:</span>
                                        <a href="mailto:{{ $application->user->email }}" class="text-sm text-primary">{{ $application->user->email }}</a>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-sm text-secondary">Vai trò:</span>
                                        <span class="badge badge-sm bg-gradient-secondary">{{ ucfirst($application->user->role) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-sm text-secondary">Ngày đăng ký:</span>
                                        <span class="text-sm">{{ $application->user->created_at->format('d/m/Y') }}</span>
                                    </div>
                                    <hr class="horizontal dark">
                                    <a href="{{ route('admin.users.show', $application->user) }}" class="btn btn-sm bg-gradient-info mb-0">
                                        <i class="fas fa-external-link-alt me-1"></i> Xem hồ sơ user
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h6 class="text-uppercase text-xs font-weight-bolder opacity-6 mb-3">
                                <i class="fas fa-link me-2"></i> Thông tin liên hệ
                            </h6>
                            <div class="card">
                                <div class="card-body pt-3">
                                    <div class="mb-3">
                                        <span class="text-xs text-secondary d-block mb-1">Facebook</span>
                                        <a href="{{ $application->facebook_link }}" target="_blank" class="text-sm">
                                            <i class="fab fa-facebook text-primary me-1"></i>{{ Str::limit($application->facebook_link, 40) }}
                                        </a>
                                    </div>
                                    @if ($application->telegram_link)
                                    <div class="mb-3">
                                        <span class="text-xs text-secondary d-block mb-1">Telegram</span>
                                        <a href="{{ $application->telegram_link }}" target="_blank" class="text-sm">
                                            <i class="fab fa-telegram text-info me-1"></i>{{ Str::limit($application->telegram_link, 40) }}
                                        </a>
                                    </div>
                                    @endif
                                    @if ($application->other_platform)
                                    <div>
                                        <span class="text-xs text-secondary d-block mb-1">Nền tảng khác</span>
                                        <span class="text-sm fw-bold">{{ $application->other_platform }}</span>
                                        @if ($application->other_platform_link)
                                        <br>
                                        <a href="{{ $application->other_platform_link }}" target="_blank" class="text-sm">
                                            <i class="fas fa-globe text-success me-1"></i>{{ Str::limit($application->other_platform_link, 35) }}
                                        </a>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <div class="mb-4">
                            <h6 class="text-uppercase text-xs font-weight-bolder opacity-6 mb-3">
                                <i class="fas fa-info-circle me-2"></i> Giới thiệu
                            </h6>
                            <div class="card">
                                <div class="card-body pt-3">
                                    <div class="text-sm" style="white-space: pre-wrap;">{{ $application->introduction ?: 'Chưa có' }}</div>
                                </div>
                            </div>
                        </div>

                        @if ($application->status != 'pending' && $application->admin_note)
                        <div class="mb-4">
                            <h6 class="text-uppercase text-xs font-weight-bolder opacity-6 mb-3">
                                <i class="fas fa-comment-alt me-2"></i> Phản hồi quản trị viên
                            </h6>
                            <div class="card border-start border-{{ $application->status == 'approved' ? 'success' : 'danger' }} border-3">
                                <div class="card-body pt-3">
                                    <div class="text-sm" style="white-space: pre-wrap;">{{ $application->admin_note }}</div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if ($application->status == 'pending')
                        <div>
                            <h6 class="text-uppercase text-xs font-weight-bolder opacity-6 mb-3">
                                <i class="fas fa-tasks me-2"></i> Hành động
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <form action="{{ route('admin.author-applications.approve', $application) }}" method="POST">
                                                @csrf
                                                <label for="approve_note" class="form-label text-sm">Ghi chú khi duyệt (tùy chọn)</label>
                                                <textarea class="form-control form-control-sm mb-3" id="approve_note" name="admin_note" rows="2" placeholder="Ghi chú..."></textarea>
                                                <button type="submit" class="btn btn-sm bg-gradient-success w-100">
                                                    <i class="fas fa-check-circle me-1"></i> Duyệt đơn
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <form action="{{ route('admin.author-applications.reject', $application) }}" method="POST">
                                                @csrf
                                                <label for="reject_note" class="form-label text-sm">Lý do từ chối <span class="text-danger">*</span></label>
                                                <textarea class="form-control form-control-sm mb-3" id="reject_note" name="admin_note" rows="2" required placeholder="Nhập lý do..."></textarea>
                                                <button type="submit" class="btn btn-sm bg-gradient-danger w-100">
                                                    <i class="fas fa-times-circle me-1"></i> Từ chối đơn
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
