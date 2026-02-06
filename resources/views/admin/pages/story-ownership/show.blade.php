@extends('admin.layouts.app')

@section('content-auth')
    <div class="row">
        <div class="col-12">

            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Chuyển quyền sở hữu & Đồng sở hữu</h5>
                        <div>
                            <a href="{{ route('admin.stories.show', $story) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Chi tiết truyện
                            </a>
                            <a href="{{ route('admin.stories.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-list me-1"></i> Danh sách truyện
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="text-uppercase text-xs font-weight-bolder opacity-6">Truyện</h6>
                        <h5>{{ $story->title }}</h5>
                        <p class="text-sm text-muted mb-0">Chủ sở hữu hiện tại:
                            <strong>{{ $story->user->name ?? 'N/A' }}</strong> ({{ $story->user->email ?? '' }})</p>
                        @if ($story->editor_id)
                            <p class="text-sm text-muted mb-0">Biên tập viên: {{ $story->editor->name ?? 'N/A' }}</p>
                        @endif
                    </div>

                    {{-- Chuyển quyền sở hữu --}}
                    <div class="card bg-light mb-4">
                        <div class="card-header py-2">
                            <h6 class="mb-0"><i class="fas fa-exchange-alt me-1"></i> Chuyển quyền sở hữu</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.story-ownership.transfer', $story) }}" method="POST"
                                class="row g-3">
                                @csrf
                                <div class="col-md-6">
                                    <label class="form-label">Chủ sở hữu mới</label>
                                    <select name="new_owner_id"
                                        class="form-select @error('new_owner_id') is-invalid @enderror" required>
                                        <option value="">-- Chọn tác giả --</option>
                                        @foreach ($authors as $author)
                                            @if ($author->id != $story->user_id)
                                                <option value="{{ $author->id }}"
                                                    {{ old('new_owner_id') == $author->id ? 'selected' : '' }}>
                                                    {{ $author->name }} ({{ $author->email }})
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('new_owner_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Ghi chú (tùy chọn)</label>
                                    <input type="text" name="note" class="form-control" value="{{ old('note') }}"
                                        placeholder="Ghi chú...">
                                    @error('note')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-sm bg-gradient-primary w-100 mb-0">
                                        <i class="fas fa-paper-plane me-1 "></i> Chuyển
                                    </button>
                                </div>
                            </form>
                            <p class="text-xs text-muted mt-2 mb-0">Sau khi chuyển, stories.user_id và stories.editor_id sẽ
                                được cập nhật sang chủ mới.</p>
                        </div>
                    </div>

                    {{-- Đồng sở hữu --}}
                    <div class="card bg-light mb-4">
                        <div class="card-header py-2 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-users me-1"></i> Đồng sở hữu (chia doanh thu combo)</h6>
                        </div>
                        <div class="card-body">
                            <p class="text-sm text-muted mb-3">Chỉ chủ sở hữu hiện tại và các đồng sở hữu được chia doanh
                                thu từ gói combo. Thu nhập mua chương lẻ luôn thuộc người tạo chương.</p>

                            <form action="{{ route('admin.story-ownership.add-co-owner', $story) }}" method="POST"
                                class="row g-3 mb-4">
                                @csrf
                                <div class="col-md-8">
                                    <label class="form-label">Thêm đồng sở hữu</label>
                                    <select name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                                        <option value="">-- Chọn tác giả --</option>
                                        @foreach ($authors as $author)
                                            @if ($author->id != $story->user_id && !$story->coOwners->contains('user_id', $author->id))
                                                <option value="{{ $author->id }}"
                                                    {{ old('user_id') == $author->id ? 'selected' : '' }}>
                                                    {{ $author->name }} ({{ $author->email }})
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-sm bg-gradient-success w-100 mb-0">
                                        <i class="fas fa-plus me-1"></i> Thêm
                                    </button>
                                </div>
                            </form>

                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Tác giả</th>
                                        <th>Email</th>
                                        <th width="120">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($story->coOwners as $co)
                                        <tr>
                                            <td>{{ $co->user->name ?? 'N/A' }}</td>
                                            <td>{{ $co->user->email ?? '' }}</td>
                                            <td>
                                                @if ($co->user_id == $story->editor_id)
                                                    <span class="badge bg-secondary">Biên tập viên - không thể xóa</span>
                                                @else
                                                    @include('admin.pages.components.delete-form', [
                                                        'id' => $co->id,
                                                        'route' => route('admin.story-ownership.remove-co-owner', [
                                                            $story,
                                                            $co,
                                                        ]),
                                                    ])
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-muted">Chưa có đồng sở hữu</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Lịch sử chuyển quyền --}}
                    <div class="card bg-light">
                        <div class="card-header py-2">
                            <h6 class="mb-0"><i class="fas fa-history me-1"></i> Lịch sử chuyển quyền</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Thời gian</th>
                                            <th>Loại</th>
                                            <th>Chi tiết</th>
                                            <th>Thực hiện bởi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($story->ownershipTransfers as $t)
                                            <tr>
                                                <td class="text-nowrap">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                                                <td><span class="badge bg-info">{{ $t->transfer_type_label }}</span></td>
                                                <td>
                                                    @if ($t->transfer_type == 'ownership_change' && $t->fromUser && $t->toUser)
                                                        {{ $t->fromUser->name }} → {{ $t->toUser->name }}
                                                    @elseif($t->affectedUser)
                                                        {{ $t->affectedUser->name }}
                                                    @endif
                                                    @if ($t->note)
                                                        <br><small
                                                            class="text-muted">{{ Str::limit($t->note, 60) }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $t->transferredBy->name ?? 'N/A' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-muted text-center py-3">Chưa có lịch sử</td>
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
    </div>
@endsection
