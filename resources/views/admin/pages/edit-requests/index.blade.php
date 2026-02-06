@extends('admin.layouts.app')

@section('title', 'Quản lý yêu cầu chỉnh sửa truyện')

@push('styles-admin')
<style>
    .status-badge { display: inline-block; padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: 500; }
    .status-pending { background-color: #fff3cd; color: #856404; }
    .status-approved { background-color: #d4edda; color: #155724; }
    .status-rejected { background-color: #f8d7da; color: #721c24; }
</style>
@endpush

@section('content-auth')
<div class="row">
    <div class="col-12">
        <div class="card mb-0 mb-md-4">
            <div class="card-header pb-0">
                <h5 class="mb-0">Yêu cầu chỉnh sửa truyện</h5>
                <p class="text-sm mb-0">Tổng: {{ $pendingCount }} chờ / {{ $approvedCount }} đã duyệt / {{ $rejectedCount }} từ chối</p>
                <div class="d-flex flex-column flex-md-row gap-2 mt-3">
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.edit-requests.index', ['status' => 'pending']) }}" class="btn btn-sm {{ (!request('status') || request('status') == 'pending') ? 'btn-warning' : 'btn-outline-warning' }}">Chờ duyệt ({{ $pendingCount }})</a>
                        <a href="{{ route('admin.edit-requests.index', ['status' => 'approved']) }}" class="btn btn-sm {{ request('status') == 'approved' ? 'btn-success' : 'btn-outline-success' }}">Đã duyệt ({{ $approvedCount }})</a>
                        <a href="{{ route('admin.edit-requests.index', ['status' => 'rejected']) }}" class="btn btn-sm {{ request('status') == 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">Từ chối ({{ $rejectedCount }})</a>
                    </div>
                    <form method="GET" class="d-flex gap-2">
                        <input type="hidden" name="status" value="{{ request('status', 'pending') }}">
                        <input type="text" class="form-control form-control-sm" name="search" value="{{ request('search') }}" placeholder="Tìm tiêu đề, tác giả..." style="min-width: 200px;">
                        <button type="submit" class="btn btn-sm bg-gradient-primary"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-xxs font-weight-bolder">ID</th>
                                <th class="text-uppercase text-xxs font-weight-bolder">Ảnh</th>
                                <th class="text-uppercase text-xxs font-weight-bolder">Tiêu đề</th>
                                <th class="text-uppercase text-xxs font-weight-bolder">Tác giả</th>
                                <th class="text-uppercase text-xxs font-weight-bolder">Ngày gửi</th>
                                <th class="text-uppercase text-xxs font-weight-bolder">Trạng thái</th>
                                <th class="text-center text-uppercase text-xxs font-weight-bolder">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($editRequests as $er)
                            <tr>
                                <td>{{ $er->id }}</td>
                                <td>
                                    @if($er->cover_thumbnail ?? $er->cover)
                                        <img src="{{ Storage::url($er->cover_thumbnail ?? $er->cover) }}" alt="" class="rounded" style="width: 45px; height: 60px; object-fit: cover;">
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ Str::limit($er->title, 40) }}</strong>
                                </td>
                                <td>{{ $er->user->name }}</td>
                                <td>{{ $er->submitted_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($er->status == 'pending')<span class="status-badge status-pending">Chờ duyệt</span>
                                    @elseif($er->status == 'approved')<span class="status-badge status-approved">Đã duyệt</span>
                                    @else<span class="status-badge status-rejected">Từ chối</span>@endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.edit-requests.show', $er) }}" class="btn btn-link p-1 mb-0 action-icon view-icon" title="Xem"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center py-4">Không có yêu cầu nào</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 pt-4">
                    <x-pagination :paginator="$editRequests" />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
