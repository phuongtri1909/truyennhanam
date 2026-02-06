@extends('admin.layouts.app')

@section('content-auth')
<div class="row">
    <div class="col-12">
        <div class="card mb-0 mb-md-4">
            <div class="card-header pb-0">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">Thống kê lượt click affiliate (Truyện Zhihu)</h5>
                    <a href="{{ route('admin.affiliate-links.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
                <p class="text-sm text-muted mb-0 mt-2">Tổng lượt click: <strong>{{ number_format($totalClicks) }}</strong></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-6">
        <div class="card mb-0 mb-md-4">
            <div class="card-header pb-0">
                <h6 class="mb-0">Theo link affiliate</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-xxs font-weight-bolder">Link</th>
                                <th class="text-center text-uppercase text-xxs font-weight-bolder">Lượt click</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($byLink as $link)
                            <tr>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ Str::limit($link->title ?? $link->url, 50) }}</p>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-gradient-primary">{{ number_format($link->clicks_count ?? 0) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center py-4 text-muted">Chưa có dữ liệu</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($byLink->hasPages())
                <div class="card-footer py-2">
                    <x-pagination :paginator="$byLink" />
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card mb-0 mb-md-4">
            <div class="card-header pb-0">
                <h6 class="mb-0">Theo truyện zhihu</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-xxs font-weight-bolder">Truyện</th>
                                <th class="text-center text-uppercase text-xxs font-weight-bolder">Lượt click</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($byStoryRows as $row)
                            <tr>
                                <td>
                                    @php $story = $stories->get($row->story_id); @endphp
                                    @if($story)
                                        <a href="{{ route('admin.stories.show', $story) }}" class="text-xs font-weight-bold text-dark text-decoration-none">{{ Str::limit($story->title, 45) }}</a>
                                    @else
                                        <span class="text-muted">Truyện #{{ $row->story_id }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-gradient-info">{{ number_format($row->clicks_count) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center py-4 text-muted">Chưa có dữ liệu</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($byStoryRows->hasPages())
                <div class="card-footer py-2">
                    <x-pagination :paginator="$byStoryRows" />
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
