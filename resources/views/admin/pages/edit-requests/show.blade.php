@extends('admin.layouts.app')

@section('title', 'Chi tiết yêu cầu chỉnh sửa #' . $editRequest->id)

@push('styles-admin')
<style>
    .story-detail { background-color: #f8f9fa; border-radius: 8px; padding: 16px; margin-bottom: 16px; }
    .comparison-container { display: flex; gap: 16px; flex-wrap: wrap; }
    .comparison-column { flex: 1; min-width: 280px; }
    .comparison-header { font-weight: 600; padding: 8px; background: #e9ecef; border-radius: 6px; margin-bottom: 8px; }
    .status-badge { display: inline-block; padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: 500; }
    .status-pending { background-color: #fff3cd; color: #856404; }
    .status-approved { background-color: #d4edda; color: #155724; }
    .status-rejected { background-color: #f8d7da; color: #721c24; }
</style>
@endpush

@section('content-auth')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Yêu cầu chỉnh sửa #{{ $editRequest->id }}</h5>
                <a href="{{ route('admin.edit-requests.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lại</a>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <span class="status-badge status-{{ $editRequest->status }}">{{ $editRequest->status == 'pending' ? 'Chờ duyệt' : ($editRequest->status == 'approved' ? 'Đã duyệt' : 'Từ chối') }}</span>
                    <span class="ms-3 text-muted">Gửi: {{ $editRequest->submitted_at->format('d/m/Y H:i') }}</span>
                    @if($editRequest->reviewed_at)
                        <span class="ms-2 text-muted">Duyệt: {{ $editRequest->reviewed_at->format('d/m/Y H:i') }}</span>
                    @endif
                    <p class="mb-0 mt-2"><strong>Tác giả:</strong> {{ $editRequest->user->name }} ({{ $editRequest->user->email }})</p>
                    @if($editRequest->review_note)
                        <p class="mb-0 mt-1"><strong>Ghi chú tác giả:</strong> {{ $editRequest->review_note }}</p>
                    @endif
                </div>

                <div class="story-detail">
                    <h6 class="mb-2">Tiêu đề</h6>
                    <div class="comparison-container">
                        <div class="comparison-column">
                            <div class="comparison-header">Hiện tại</div>
                            <div>{{ $story->title }}</div>
                        </div>
                        <div class="comparison-column">
                            <div class="comparison-header">Yêu cầu thay đổi</div>
                            <div class="{{ $story->title != $editRequest->title ? 'text-primary fw-bold' : '' }}">{{ $editRequest->title }}</div>
                        </div>
                    </div>
                </div>

                <div class="story-detail">
                    <h6 class="mb-2">Slug</h6>
                    <div class="comparison-container">
                        <div class="comparison-column"><div class="comparison-header">Hiện tại</div><div>{{ $story->slug }}</div></div>
                        <div class="comparison-column"><div class="comparison-header">Yêu cầu</div><div class="{{ $story->slug != $editRequest->slug ? 'text-primary fw-bold' : '' }}">{{ $editRequest->slug }}</div></div>
                    </div>
                </div>

                <div class="story-detail">
                    <h6 class="mb-2">Mô tả</h6>
                    <div class="comparison-container">
                        <div class="comparison-column"><div class="comparison-header">Hiện tại</div><div class="small">{!! Str::limit(strip_tags($story->description), 300) !!}</div></div>
                        <div class="comparison-column"><div class="comparison-header">Yêu cầu</div><div class="small {!! $story->description != $editRequest->description ? 'text-primary' : '' !!}">{!! Str::limit(strip_tags($editRequest->description), 300) !!}</div></div>
                    </div>
                </div>

                <div class="story-detail">
                    <h6 class="mb-2">Tên tác giả / 18+ / Combo</h6>
                    <div class="comparison-container">
                        <div class="comparison-column">
                            <div class="comparison-header">Hiện tại</div>
                            <div>{{ $story->author_name }} | {{ $story->is_18_plus ? '18+' : '-' }} | {{ $story->has_combo ? 'Combo ' . $story->combo_price : '-' }}</div>
                        </div>
                        <div class="comparison-column">
                            <div class="comparison-header">Yêu cầu</div>
                            <div>{{ $editRequest->author_name }} | {{ $editRequest->is_18_plus ? '18+' : '-' }} | {{ $editRequest->has_combo ? 'Combo ' . $editRequest->combo_price : '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="story-detail">
                    <h6 class="mb-2">Thể loại</h6>
                    <div class="comparison-container">
                        <div class="comparison-column">
                            <div class="comparison-header">Hiện tại</div>
                            <div>@foreach($story->categories as $c)<span class="badge bg-info me-1">{{ $c->name }}</span>@endforeach</div>
                        </div>
                        <div class="comparison-column">
                            <div class="comparison-header">Yêu cầu</div>
                            <div>
                                @php $reqCats = \App\Models\Category::whereIn('id', $editRequest->category_ids)->get(); @endphp
                                @foreach($reqCats as $c)<span class="badge bg-info me-1">{{ $c->name }}</span>@endforeach
                            </div>
                        </div>
                    </div>
                </div>

                @if($editRequest->cover)
                <div class="story-detail">
                    <h6 class="mb-2">Ảnh bìa</h6>
                    <div class="comparison-container">
                        <div class="comparison-column">
                            <div class="comparison-header">Hiện tại</div>
                            <img src="{{ Storage::url($story->cover_thumbnail ?? $story->cover) }}" alt="" class="rounded" style="max-height: 180px; object-fit: cover;">
                        </div>
                        <div class="comparison-column">
                            <div class="comparison-header">Yêu cầu</div>
                            <img src="{{ Storage::url($editRequest->cover_thumbnail ?? $editRequest->cover) }}" alt="" class="rounded" style="max-height: 180px; object-fit: cover;">
                        </div>
                    </div>
                </div>
                @endif

                @if($editRequest->status == 'pending')
                <div class="card mt-4">
                    <div class="card-body">
                        <h6 class="mb-3">Hành động</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <form action="{{ route('admin.edit-requests.approve', $editRequest) }}" method="POST">
                                    @csrf
                                    <label class="form-label">Ghi chú khi duyệt (tùy chọn)</label>
                                    <textarea name="admin_note" class="form-control mb-2" rows="2" placeholder="Ghi chú..."></textarea>
                                    <button type="submit" class="btn btn-success w-100"><i class="fas fa-check me-1"></i> Duyệt</button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form action="{{ route('admin.edit-requests.reject', $editRequest) }}" method="POST">
                                    @csrf
                                    <label class="form-label">Lý do từ chối <span class="text-danger">*</span></label>
                                    <textarea name="admin_note" class="form-control mb-2" rows="2" required placeholder="Nhập lý do..."></textarea>
                                    <button type="submit" class="btn btn-danger w-100"><i class="fas fa-times me-1"></i> Từ chối</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @elseif($editRequest->admin_note)
                <div class="alert alert-secondary mt-3">
                    <strong>Phản hồi admin:</strong><br>{{ $editRequest->admin_note }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
