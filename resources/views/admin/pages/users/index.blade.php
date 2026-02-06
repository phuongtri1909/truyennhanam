@extends('admin.layouts.app')

@push('styles-admin')
<style>
    @media (max-width: 768px) {
        .w-md-auto {
            width: 100% !important;
        }
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
                            <h5 class="mb-0">Danh sách người dùng</h5>
                            <p class="text-sm mb-0">
                                Tổng: {{ $stats['total'] }} người dùng
                                <span class="d-inline-flex flex-wrap gap-2 mt-1">
                                    <a href="{{ route('admin.users.index', array_merge(request()->except('role'), ['role' => 'admin_main'])) }}" class="badge {{ request('role') == 'admin_main' ? 'bg-gradient-danger' : 'bg-light text-dark' }} text-decoration-none">{{ $stats['admin_main'] }} Admin</a>
                                    <a href="{{ route('admin.users.index', array_merge(request()->except('role'), ['role' => 'admin_sub'])) }}" class="badge {{ request('role') == 'admin_sub' ? 'bg-gradient-info' : 'bg-light text-dark' }} text-decoration-none">{{ $stats['admin_sub'] }} Admin Sub</a>
                                    <a href="{{ route('admin.users.index', array_merge(request()->except('role'), ['role' => 'author'])) }}" class="badge {{ request('role') == 'author' ? 'bg-gradient-success' : 'bg-light text-dark' }} text-decoration-none">{{ $stats['author'] }} Tác giả</a>
                                    <a href="{{ route('admin.users.index', array_merge(request()->except('role'), ['role' => 'user'])) }}" class="badge {{ request('role') == 'user' ? 'bg-gradient-warning' : 'bg-light text-dark' }} text-decoration-none">{{ $stats['user'] }} User</a>
                                    @if(request('role'))
                                    <a href="{{ route('admin.users.index', request()->except('role')) }}" class="badge bg-secondary text-decoration-none">Tất cả</a>
                                    @endif
                                </span>
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.ban-ips.index') }}" class="btn btn-danger btn-sm">
                                <i class="fas fa-ban me-1"></i> Quản lý Ban IP
                            </a>
                        </div>
                    </div>
                    <form action="{{ route('admin.users.index') }}" method="GET" class="mt-3 d-flex flex-column flex-md-row gap-2">
                        <div class="d-flex flex-column flex-md-row gap-2 mb-2 mb-md-0">
                            <select name="role" class="form-select form-select-sm w-100 w-md-auto">
                                <option value="">Tất cả vai trò</option>
                                <option value="admin_main" {{ request('role') == 'admin_main' ? 'selected' : '' }}>Admin</option>
                                <option value="admin_sub" {{ request('role') == 'admin_sub' ? 'selected' : '' }}>Admin Sub</option>
                                <option value="author" {{ request('role') == 'author' ? 'selected' : '' }}>Tác giả</option>
                                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                            </select>

                            <input type="text" name="ip" class="form-control form-control-sm w-100 w-md-auto"
                                   placeholder="Địa chỉ IP" value="{{ request('ip') }}">

                            <input type="date" name="date" class="form-control form-control-sm w-100 w-md-auto"
                                   value="{{ request('date') }}">
                        </div>

                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" name="search"
                                   placeholder="Tìm kiếm..." value="{{ request('search') }}">
                            <button class="btn bg-gradient-primary btn-sm px-2 mb-0" type="submit">
                                <i class="fa-solid fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-body px-0 pt-0 pb-2">

                    

                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>

                                    <th class="text-uppercase text-xxs font-weight-bolder ps-2">Avatar</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder ps-2">Tên</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">Email</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">Vai trò</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">IP</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">Số nấm</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">Ngày tạo</th>
                                    <th
                                        class="text-center text-uppercase  text-xxs font-weight-bolder ">
                                        Hành động
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                <tr>

                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            @if ($user->avatar == null)
                                                <img src="{{ asset('/images/defaults/avatar_default.jpg') }}" class="avatar avatar-sm me-3" alt="user1">
                                            @else
                                                <img src="{{ Storage::url($user->avatar) }}" class="avatar avatar-sm me-3" alt="user1">
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $user->name }}</p>
                                    </td>

                                    <td class="text-center">
                                        <p class="text-xs font-weight-bold mb-0">{{ $user->email }}</p>
                                    </td>
                                    <td class="text-center">
                                        <p class="text-xs font-weight-bold mb-0">
                                            @php
                                                $roleBadge = match($user->role) {
                                                    'admin_main' => ['bg-gradient-danger', 'Admin'],
                                                    'admin_sub' => ['bg-gradient-info', 'Admin Sub'],
                                                    'author' => ['bg-gradient-success', 'Tác giả'],
                                                    'user' => ['bg-gradient-warning', 'User'],
                                                    default => ['bg-gradient-secondary', $user->role ?? '—']
                                                };
                                            @endphp
                                            <span class="badge {{ $roleBadge[0] }}">{{ $roleBadge[1] }}</span>
                                            @if ($user->role == 'author' && ($user->can_publish_zhihu ?? false))
                                                <span class="badge bg-gradient-dark ms-1" title="Được đăng truyện Zhihu">Zhihu</span>
                                            @endif
                                        </p>
                                    </td>
                                    <td class="text-center">
                                        <p class="text-xs font-weight-bold mb-0">{{ $user->ip_address ?: '—' }}</p>
                                    </td>
                                    <td class="text-center">
                                        <p class="text-xs font-weight-bold mb-0">{{ number_format($user->coins ?? 0) }} nấm</p>
                                    </td>
                                    <td class="text-center">
                                        <p class="text-xs font-weight-bold mb-0">{{ $user->created_at?->format('d/m/Y H:i') ?? '—' }}</p>
                                    </td>

                                    <td class="text-center">
                                        <div class="d-flex flex-wrap justify-content-center">
                                            <div class="d-flex flex-column align-items-center mb-2 me-2">
                                                <a href="{{ route('admin.users.show', ['user' => $user]) }}" class="btn btn-link p-1 mb-0 action-icon view-icon" title="Chi tiết">
                                                    <i class="fas fa-eye text-white"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <x-pagination :paginator="$users->withQueryString()" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="actionModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="actionModalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('cancel') }}</button>
                    <button type="button" class="btn bg-gradient-primary" id="confirmAction"></button>
                </div>
            </div>
        </div>
    </div>


    <form id="actionForm" method="post" style="display: none;">
        @csrf
        <input type="hidden" name="item_id" id="formItemId">
    </form>
@endsection
@push('scripts-admin')
<script>

</script>
@endpush
