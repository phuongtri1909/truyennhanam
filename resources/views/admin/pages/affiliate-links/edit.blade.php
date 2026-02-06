@extends('admin.layouts.app')

@section('content-auth')
<div class="row">
    <div class="col-12">
        <div class="card mb-0 mb-md-4">
            <div class="card-header pb-0 px-3">
                <h5 class="mb-0">Sửa link Affiliate #{{ $affiliateLink->id }}</h5>
            </div>
            <div class="card-body pt-4 p-3">
                <form action="{{ route('admin.affiliate-links.update', $affiliateLink) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-3">
                        <label for="url">URL Shopee <span class="text-danger">*</span></label>
                        <input type="url" name="url" id="url" class="form-control @error('url') is-invalid @enderror" value="{{ old('url', $affiliateLink->url) }}" required>
                        @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group mb-3">
                        <label for="title">Tiêu đề (mô tả)</label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $affiliateLink->title) }}">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group mb-3">
                        <label>Banner hiện tại</label>
                        @if($affiliateLink->banner_path)
                            <div class="mb-2"><img src="{{ Storage::url($affiliateLink->banner_path) }}" alt="Banner" class="img-fluid rounded" style="max-height: 100px;"></div>
                        @else
                            <p class="text-muted small mb-2">Chưa có ảnh</p>
                        @endif
                        <label for="banner">Thay ảnh mới (tùy chọn)</label>
                        <input type="file" name="banner" id="banner" class="form-control" accept="image/*">
                        @error('banner')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $affiliateLink->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Đang hoạt động</label>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn bg-gradient-primary">Cập nhật</button>
                        <a href="{{ route('admin.affiliate-links.index') }}" class="btn btn-secondary">Trở về</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
