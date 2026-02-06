@extends('admin.layouts.app')

@section('content-auth')
    <div class="row">
        <div class="col-12">

            <div class="card mb-0 mb-md-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Góp ý #{{ $webFeedback->id }}</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.web-feedback.index') }}" class="btn btn-secondary btn-sm">Quay lại</a>
                        @if($webFeedback->read_at)
                            <form action="{{ route('admin.web-feedback.mark-unread', $webFeedback) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm">Đánh dấu chưa đọc</button>
                            </form>
                        @else
                            <form action="{{ route('admin.web-feedback.mark-read', $webFeedback) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">Đánh dấu đã đọc</button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-uppercase text-xs text-muted">Người gửi</label>
                            <p class="mb-0">{{ $webFeedback->user->name ?? 'N/A' }} ({{ $webFeedback->user->email ?? '' }})</p>
                            <a href="{{ route('admin.users.show', $webFeedback->user_id) }}" class="text-sm">Xem trang user</a>
                        </div>
                        <div class="col-md-6">
                            <label class="text-uppercase text-xs text-muted">Mức độ mong muốn cải thiện</label>
                            <p class="mb-0">
                                @php $labels = \App\Models\WebFeedback::intensityLabels(); @endphp
                                <span class="badge bg-info">{{ $labels[$webFeedback->intensity_level] ?? $webFeedback->intensity_level }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="text-uppercase text-xs text-muted">Ngày gửi</label>
                            <p class="mb-0">{{ $webFeedback->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-uppercase text-xs text-muted">Nội dung góp ý</label>
                        <div class="p-3 bg-light rounded border">{{ nl2br(e($webFeedback->content)) }}</div>
                    </div>
                    @if($webFeedback->read_at)
                        <p class="text-muted small mb-0">Đã đọc lúc: {{ $webFeedback->read_at->format('d/m/Y H:i') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
