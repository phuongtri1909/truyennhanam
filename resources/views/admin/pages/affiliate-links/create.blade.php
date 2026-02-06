@extends('admin.layouts.app')

@section('content-auth')
<div class="row">
    <div class="col-12">
        <div class="card mb-0 mb-md-4">
            <div class="card-header pb-0 px-3">
                <h5 class="mb-0">Thêm link Affiliate Shopee</h5>
            </div>
            <div class="card-body pt-4 p-3">
                <form action="{{ route('admin.affiliate-links.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="url">URL Shopee <span class="text-danger">*</span></label>
                        <input type="url" name="url" id="url" class="form-control @error('url') is-invalid @enderror" value="{{ old('url') }}" placeholder="https://s.shopee.vn/..." required>
                        @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group mb-3">
                        <label for="title">Tiêu đề (mô tả)</label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="VD: Combo OMO 3 trong 1...">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group mb-3">
                        <label for="banner">Ảnh banner (tùy chọn)</label>
                        <input type="file" name="banner" id="banner" class="form-control" accept="image/*">
                        <small class="text-muted">JPG, PNG, GIF. Tối đa 2MB.</small>
                        @error('banner')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Đang hoạt động</label>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn bg-gradient-primary">Lưu</button>
                        <a href="{{ route('admin.affiliate-links.index') }}" class="btn btn-secondary">Trở về</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
