@extends('admin.layouts.app')

@section('content-auth')
    <div class="row">
        <div class="col-12">

            <div class="card mb-0 mb-md-4">
                <div class="card-header pb-0">
                    <h5 class="mb-0">Góp ý cải thiện web</h5>
                    <p class="text-sm text-muted mb-0">Danh sách góp ý từ người dùng</p>
                </div>
                <div class="card-body px-3">
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label for="read" class="form-label">Trạng thái</label>
                            <select name="read" id="read" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="0" {{ request('read') === '0' ? 'selected' : '' }}>Chưa đọc</option>
                                <option value="1" {{ request('read') === '1' ? 'selected' : '' }}>Đã đọc</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <input type="text" name="search" id="search" class="form-control" placeholder="Nội dung, tên, email..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn bg-gradient-primary btn-sm me-2 mb-0"><i class="fas fa-search"></i> Lọc</button>
                            <a href="{{ route('admin.web-feedback.index') }}" class="btn btn-secondary btn-sm mb-0">Xóa lọc</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-xxs font-weight-bolder opacity-7">ID</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder opacity-7">Người gửi</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder opacity-7">Mức độ</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder opacity-7">Nội dung</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder opacity-7">Đã đọc</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder opacity-7">Ngày gửi</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder opacity-7">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($feedbacks as $fb)
                                    <tr class="{{ $fb->read_at ? '' : 'table-warning' }}">
                                        <td><span class="text-xs font-weight-bold">{{ $fb->id }}</span></td>
                                        <td>
                                            <span class="text-sm">{{ $fb->user->name ?? 'N/A' }}</span>
                                            <br><span class="text-xs text-muted">{{ $fb->user->email ?? '' }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $labels = \App\Models\WebFeedback::intensityLabels();
                                            @endphp
                                            <span class="badge bg-info">{{ $labels[$fb->intensity_level] ?? $fb->intensity_level }}</span>
                                        </td>
                                        <td style="max-width: 280px;"><span class="text-xs">{{ Str::limit($fb->content, 100) }}</span></td>
                                        <td>
                                            @if($fb->read_at)
                                                <span class="badge bg-success">Đã đọc</span>
                                                <br><small class="text-muted">{{ $fb->read_at->format('d/m/Y H:i') }}</small>
                                            @else
                                                <span class="badge bg-warning">Chưa đọc</span>
                                            @endif
                                        </td>
                                        <td><span class="text-xs">{{ $fb->created_at->format('d/m/Y H:i') }}</span></td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.web-feedback.show', $fb) }}" class="btn btn-info btn-sm" title="Xem"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">Chưa có góp ý nào.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $feedbacks->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
