@extends('admin.layouts.app')

@section('content-auth')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Từ khóa nhạy cảm</h6>
                    <p class="text-sm text-muted mb-0">Các từ trong danh sách sẽ được làm mờ (****) khi hiển thị nội dung chapter. Người đọc có thể click để xem.</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.sensitive-keywords.store') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Thêm từ khóa</label>
                                <input type="text" name="keyword" class="form-control @error('keyword') is-invalid @enderror"
                                    placeholder="Nhập từ khóa cần mã hóa" value="{{ old('keyword') }}" autocomplete="off">
                                @error('keyword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn bg-gradient-primary btn-sm w-100 mb-0">
                                    <i class="fas fa-plus me-1"></i> Thêm
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-xxs font-weight-bolder">#</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder ps-2">Từ khóa</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder ps-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($keywords as $index => $kw)
                                <tr>
                                    <td><span class="text-sm">{{ $index + 1 }}</span></td>
                                    <td><span class="text-sm font-weight-bold">{{ $kw->keyword }}</span></td>
                                    <td>
                                        <form action="{{ route('admin.sensitive-keywords.destroy', $kw) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa từ khóa này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger btn-sm p-0" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Chưa có từ khóa nào.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
