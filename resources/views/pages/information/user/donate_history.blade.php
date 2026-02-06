@extends('layouts.information')

@section('info_title', 'Lịch sử donate')
@section('info_description', 'Lịch sử donate của bạn trên ' . request()->getHost())
@section('info_keyword', 'Lịch sử donate, donate cám')
@section('info_section_title', 'Lịch sử donate')
@section('info_section_desc', 'Các giao dịch donate bạn đã gửi hoặc nhận')

@section('info_content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="mb-3">
                    <a href="{{ route('user.donate') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i>Quay lại Donate cám
                    </a>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <form method="GET" class="row g-3 align-items-center">
                            <div class="col-auto">
                                <label class="col-form-label">Loại:</label>
                            </div>
                            <div class="col-md-3">
                                <select name="type" class="form-select">
                                    <option value="" {{ ($filterType ?? '') === '' ? 'selected' : '' }}>Tất cả</option>
                                    <option value="sent" {{ ($filterType ?? '') === 'sent' ? 'selected' : '' }}>Đã gửi</option>
                                    <option value="received" {{ ($filterType ?? '') === 'received' ? 'selected' : '' }}>Đã nhận</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-filter me-1"></i>Lọc
                                </button>
                                <a href="{{ route('user.donate.history') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Xóa lọc
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Thời gian</th>
                                <th>Loại</th>
                                <th>Người gửi</th>
                                <th>Người nhận</th>
                                <th>Số cám</th>
                                <th>Phí</th>
                                <th>Nhận được</th>
                                <th>Lời nhắn</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($donates as $donate)
                                @php
                                    $isSent = $donate->sender_id === auth()->id();
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span>{{ $donate->created_at->format('d/m/Y') }}</span>
                                            <small class="text-muted">{{ $donate->created_at->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($isSent)
                                            <span class="badge bg-danger">Đã gửi</span>
                                        @else
                                            <span class="badge bg-success">Đã nhận</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-medium">{{ $donate->sender->name }}</span>
                                        <br><small class="text-muted">{{ $donate->sender->email }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-medium">{{ $donate->recipient->name }}</span>
                                        <br><small class="text-muted">{{ $donate->recipient->email }}</small>
                                    </td>
                                    <td>{{ number_format($donate->amount) }} cám</td>
                                    <td>
                                        @if($donate->fee_amount > 0)
                                            <span class="text-muted">-{{ number_format($donate->fee_amount) }}</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @if($isSent)
                                            <span class="text-muted" title="Người nhận nhận được">{{ number_format($donate->received_amount) }} cám</span>
                                        @else
                                            <span class="fw-bold text-success">+{{ number_format($donate->received_amount) }} cám</span>
                                        @endif
                                    </td>
                                    <td><small>{{ Str::limit($donate->message, 100) }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-heart fa-3x mb-3"></i>
                                            <p class="mb-0">Chưa có giao dịch donate nào</p>
                                            <a href="{{ route('user.donate') }}" class="btn btn-outline-primary btn-sm mt-2">Đi donate</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($donates->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $donates->links('components.pagination') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
