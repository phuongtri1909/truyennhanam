@extends('admin.layouts.app')

@section('title', 'Quản lý đơn đăng ký tác giả')

@push('styles-admin')
<style>
    @media (max-width: 768px) {
        .d-flex.flex-column.flex-md-row { flex-direction: column !important; }
        .d-flex.flex-wrap.gap-2 { flex-direction: column; gap: 0.5rem !important; }
        .d-flex.flex-wrap.gap-2 > * { width: 100%; }
        .table-responsive { font-size: 0.875rem; }
    }
</style>
@endpush

@section('content-auth')
<div class="row">
    <div class="col-12">
        <div class="card mb-0 mb-md-4">
            <div class="card-header pb-0">
                <div class="d-flex flex-row justify-content-between">
                    <div>
                        <h5 class="mb-0">Đơn đăng ký tác giả</h5>
                        <p class="text-sm mb-0">
                            Tổng: {{ $pendingCount }} chờ duyệt / {{ $approvedCount }} đã duyệt / {{ $rejectedCount }} từ chối
                        </p>
                    </div>
                </div>

                <div class="d-flex flex-column flex-md-row justify-content-between mt-3 gap-3">
                    <form method="GET" class="d-flex flex-wrap gap-2" id="filterForm">
                        <div style="min-width: 150px;">
                            <select name="status" class="form-select form-select-sm">
                                <option value="pending" {{ (!request('status') || request('status') == 'pending') ? 'selected' : '' }}>Chờ duyệt ({{ $pendingCount }})</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt ({{ $approvedCount }})</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối ({{ $rejectedCount }})</option>
                            </select>
                        </div>
                        <div class="input-group input-group-sm" style="min-width: 220px;">
                            <input type="text" class="form-control w-auto" name="search"
                                   value="{{ request('search') }}" placeholder="Tìm theo tên, email...">
                            <button class="btn bg-gradient-primary btn-sm px-3 mb-0" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-center text-uppercase text-xxs font-weight-bolder">ID</th>
                                <th class="text-uppercase text-xxs font-weight-bolder ps-2">Người dùng</th>
                                <th class="text-uppercase text-xxs font-weight-bolder">Liên hệ</th>
                                <th class="text-uppercase text-xxs font-weight-bolder">Ngày gửi</th>
                                <th class="text-center text-uppercase text-xxs font-weight-bolder">Trạng thái</th>
                                <th class="text-center text-uppercase text-xxs font-weight-bolder">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($applications as $application)
                            <tr>
                                <td class="text-center">{{ $application->id }}</td>
                                <td class="ps-2">
                                    <div class="d-flex flex-column">
                                        <h6 class="mb-0 text-sm">{{ $application->user->name }}</h6>
                                        <p class="text-xs text-secondary mb-0">{{ $application->user->email }}</p>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ $application->facebook_link }}" target="_blank" class="text-primary text-xs me-2">
                                        <i class="fab fa-facebook me-1"></i>FB
                                    </a>
                                    @if ($application->telegram_link)
                                    <a href="{{ $application->telegram_link }}" target="_blank" class="text-info text-xs">
                                        <i class="fab fa-telegram me-1"></i>TG
                                    </a>
                                    @endif
                                </td>
                                <td class="text-sm">{{ $application->submitted_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    @if ($application->status == 'pending')
                                        <span class="badge badge-sm bg-gradient-warning">Chờ duyệt</span>
                                    @elseif ($application->status == 'approved')
                                        <span class="badge badge-sm bg-gradient-success">Đã duyệt</span>
                                    @else
                                        <span class="badge badge-sm bg-gradient-danger">Từ chối</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.author-applications.show', $application) }}"
                                       class="btn btn-link text-info p-1 mb-0 action-icon view-icon" title="Xem chi tiết">
                                        <i class="fas fa-eye text-white"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Không có đơn đăng ký nào</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 pt-4">
                    <x-pagination :paginator="$applications" />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts-admin')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#filterForm select').forEach(select => {
        select.addEventListener('change', () => document.getElementById('filterForm').submit());
    });
});
</script>
@endpush
