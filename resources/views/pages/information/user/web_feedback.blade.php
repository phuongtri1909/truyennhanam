@extends('layouts.information')

@section('info_title', 'Góp ý cải thiện web - ' . config('app.name'))
@section('info_description', 'Gửi góp ý giúp chúng mình cải thiện website')
@section('info_keyword', 'góp ý, cải thiện web, phản hồi')

@section('info_section_title', 'Góp ý cải thiện web')
@section('info_section_desc', 'Gửi góp ý của bạn để chúng mình ngày càng hoàn thiện hơn')

@section('info_content')
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">

            <p class="text-muted mb-4">Bạn muốn web cải thiện vấn đề gì? Hãy gửi góp ý bên dưới. Admin sẽ xem và phản hồi.</p>

            <form action="{{ route('user.feedback.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="intensity_level" class="form-label fw-semibold">Mức độ mong muốn web cải thiện vấn đề này <span class="text-danger">*</span></label>
                    <select name="intensity_level" id="intensity_level" class="form-select form-select-lg @error('intensity_level') is-invalid @enderror" required>
                        <option value="">-- Chọn mức độ --</option>
                        @foreach(\App\Models\WebFeedback::intensityLabels() as $value => $label)
                            <option value="{{ $value }}" {{ old('intensity_level') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('intensity_level')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label for="content" class="form-label fw-semibold">Nội dung góp ý <span class="text-danger">*</span></label>
                    <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" rows="6" placeholder="Mô tả chi tiết góp ý của bạn (tối thiểu 10 ký tự)..." required>{{ old('content') }}</textarea>
                    <small class="text-muted">Tối đa 2000 ký tự.</small>
                    @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button type="submit" id="submitFeedbackBtn" class="btn bg-7 text-white fw-semibold px-4 py-2">
                    <i class="fa-solid fa-paper-plane me-2"></i> Gửi góp ý
                </button>
            </form>
        </div>
    </div>
@endsection

@push('info_scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('submitFeedbackBtn');
    if (btn) {
        btn.addEventListener('click', function() {
            setTimeout(function() {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Đang gửi...';
            }, 0);
        });
    }
});
</script>
@endpush
