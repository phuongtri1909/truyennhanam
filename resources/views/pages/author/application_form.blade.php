<div class="author-application-form-wrapper">
    <form action="{{ route('author.submit') }}" method="POST" class="author-application-form">
        @csrf

        <div class="author-form-info-banner">
            <div class="author-form-info-icon">
                <i class="fa-solid fa-lightbulb"></i>
            </div>
            <div class="author-form-info-content">
                <h6 class="author-form-info-title">Lưu ý khi đăng ký</h6>
                <p class="author-form-info-text mb-0">Để trở thành tác giả, bạn cần cung cấp thông tin liên hệ và giới thiệu bản thân. Đơn đăng ký sẽ được xem xét và phản hồi trong vòng 24-48 giờ.</p>
            </div>
        </div>

        <div class="author-form-card">
            <h6 class="author-form-section-title">
                <i class="fa-solid fa-share-nodes me-2"></i> Thông tin liên hệ
            </h6>

            <div class="form-group author-form-group">
                <label for="facebook_link" class="author-form-label">
                    Link Facebook <span class="text-danger">*</span>
                </label>
                <div class="author-input-wrapper">
                    <span class="author-input-icon"><i class="fa-brands fa-facebook"></i></span>
                    <input type="url" class="form-control author-form-input validate-url @error('facebook_link') is-invalid @enderror"
                           id="facebook_link" name="facebook_link"
                           placeholder="https://facebook.com/profile"
                           value="{{ old('facebook_link') }}" required>
                </div>
                @error('facebook_link')
                    <div class="author-form-error">{{ $message }}</div>
                @else
                    <small class="author-form-hint">Link Facebook cá nhân để liên hệ</small>
                @enderror
            </div>

            <div class="form-group author-form-group">
                <label for="telegram_link" class="author-form-label">Link Telegram</label>
                <div class="author-input-wrapper">
                    <span class="author-input-icon"><i class="fa-brands fa-telegram"></i></span>
                    <input type="url" class="form-control author-form-input validate-url @error('telegram_link') is-invalid @enderror"
                           id="telegram_link" name="telegram_link"
                           placeholder="https://t.me/username"
                           value="{{ old('telegram_link') }}">
                </div>
                @error('telegram_link')
                    <div class="author-form-error">{{ $message }}</div>
                @else
                    <small class="author-form-hint">Link Telegram (nếu có)</small>
                @enderror
            </div>

            <div class="form-group author-form-group">
                <label for="other_platform" class="author-form-label">
                    Nền tảng khác <span class="text-danger">*</span>
                </label>
                <div class="author-input-wrapper">
                    <span class="author-input-icon"><i class="fa-solid fa-globe"></i></span>
                    <input type="text" class="form-control author-form-input @error('other_platform') is-invalid @enderror"
                           id="other_platform" name="other_platform"
                           placeholder="Wattpad, Truyenfull, Facebook..."
                           value="{{ old('other_platform') }}" required>
                </div>
                @error('other_platform')
                    <div class="author-form-error">{{ $message }}</div>
                @else
                    <small class="author-form-hint">Nền tảng bạn đã từng đăng truyện</small>
                @enderror
            </div>

            <div class="form-group author-form-group">
                <label for="other_platform_link" class="author-form-label">
                    Link nền tảng khác <span class="text-danger">*</span>
                </label>
                <div class="author-input-wrapper">
                    <span class="author-input-icon"><i class="fa-solid fa-link"></i></span>
                    <input type="url" class="form-control author-form-input validate-url @error('other_platform_link') is-invalid @enderror"
                           id="other_platform_link" name="other_platform_link"
                           placeholder="https://example.com/profile"
                           value="{{ old('other_platform_link') }}" required>
                </div>
                @error('other_platform_link')
                    <div class="author-form-error">{{ $message }}</div>
                @else
                    <small class="author-form-hint">Link trang cá nhân trên nền tảng đó</small>
                @enderror
            </div>
        </div>

        <div class="author-form-card">
            <h6 class="author-form-section-title">
                <i class="fa-solid fa-pen-nib me-2"></i> Giới thiệu bản thân
            </h6>

            <div class="form-group author-form-group">
                <label for="introduction" class="author-form-label">
                    Giới thiệu <small class="text-muted">(ít nhất 50 ký tự)</small>
                </label>
                <textarea class="form-control author-form-textarea @error('introduction') is-invalid @enderror"
                          id="introduction" name="introduction" rows="5"
                          placeholder="Giới thiệu về bạn, kinh nghiệm viết truyện, thể loại sở trường..."
                          minlength="50" maxlength="1000">{{ old('introduction') }}</textarea>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <small class="author-form-hint">Kinh nghiệm, thể loại sở trường, phong cách viết...</small>
                    <span class="char-counter author-char-counter" id="charCounter">0/1000</span>
                </div>
                @error('introduction')
                    <div class="author-form-error mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="author-form-submit-wrapper">
            <button type="submit" class="btn author-form-submit-btn">
                <i class="fa-solid fa-paper-plane me-2"></i> Gửi đơn đăng ký
            </button>
        </div>
    </form>
</div> 