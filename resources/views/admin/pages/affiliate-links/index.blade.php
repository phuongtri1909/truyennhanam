@extends('admin.layouts.app')

@section('content-auth')
    <div class="row">
        <div class="col-12">
            <div class="card mb-0 mb-md-4">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0">Link Affiliate Shopee (Truyện Zhihu)</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.affiliate-links.stats') }}" class="btn btn-outline-primary btn-sm mb-0">
                                <i class="fas fa-chart-bar me-1"></i> Thống kê lượt click
                            </a>
                            <a href="{{ route('admin.affiliate-links.create') }}"
                                class="btn bg-gradient-primary btn-sm mb-0">
                                <i class="fas fa-plus"></i> Thêm link
                            </a>
                        </div>
                    </div>
                    <p class="text-sm text-muted mb-0 mt-2">Link hiển thị ngẫu nhiên trong quảng cáo affiliate trên truyện
                        zhihu.</p>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-xxs font-weight-bolder">ID</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder">Banner</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder">Tiêu đề</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder">URL</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder">Trạng thái</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">Lượt click</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($links as $link)
                                    <tr>
                                        <td class="ps-4">
                                            <p class="text-xs font-weight-bold mb-0">{{ $link->id }}</p>
                                        </td>
                                        <td>
                                            @if ($link->banner_path)
                                                <img src="{{ Storage::url($link->banner_path) }}" alt="Banner"
                                                    class="img-fluid rounded" style="max-height: 50px;">
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <p class="text-xs mb-0">{{ Str::limit($link->title ?? '-', 40) }}</p>
                                        </td>
                                        <td><a href="{{ $link->url }}" target="_blank"
                                                class="text-xs">{{ Str::limit($link->url, 50) }}</a></td>
                                        <td>
                                            <span
                                                class="badge badge-sm {{ $link->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                                                {{ $link->is_active ? 'Bật' : 'Tắt' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.affiliate-links.stats') }}?link={{ $link->id }}"
                                                class="text-xs font-weight-bold">{{ number_format($link->clicks_count ?? 0) }}</a>
                                        </td>
                                        <td class="text-center d-flex justify-content-center">
                                            <a href="{{ route('admin.affiliate-links.edit', $link) }}"
                                                class="mx-2 action-icon edit-icon" title="Sửa"><i
                                                    class="fas fa-pencil-alt"></i></a>
                                            @include('admin.pages.components.delete-form', [
                                                'id' => $link->id,
                                                'route' => route('admin.affiliate-links.destroy', $link),
                                            ])
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">Chưa có link nào. <a
                                                href="{{ route('admin.affiliate-links.create') }}">Thêm link</a></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($links->hasPages())
                        <div class="px-4 pt-4">
                            <x-pagination :paginator="$links" />
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
