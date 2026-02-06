<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 "
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        @php
            $logoPath = $logoSite && $logoSite->logo ? Storage::url($logoSite->logo) : asset('images/logo/logo-site.png');
        @endphp
        <a class="d-flex m-0 justify-content-center text-wrap" href="{{ route('home') }}">
            <img height="70" class="logof_site" src="{{ $logoPath }}" alt="{{ config('app.name') }} logo">
        </a>
    </div>
    <hr class="horizontal dark mt-0">

    <div class="docs-info">
        <a href="{{ route('home') }}" class="btn btn-white btn-sm w-100 mb-0">Trang chủ</a>
    </div>

    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            {{-- Dashboard --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <svg width="12px" height="12px" viewBox="0 0 45 40" version="1.1"
                            xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <g transform="translate(-1716.000000, -439.000000)" fill="#FFFFFF" fill-rule="nonzero">
                                    <g transform="translate(1716.000000, 291.000000)">
                                        <g transform="translate(0.000000, 148.000000)">
                                            <path class="color-background opacity-6"
                                                d="M46.7199583,10.7414583 L40.8449583,0.949791667 C40.4909749,0.360605034 39.8540131,0 39.1666667,0 L7.83333333,0 C7.1459869,0 6.50902508,0.360605034 6.15504167,0.949791667 L0.280041667,10.7414583 C0.0969176761,11.0460037 -1.23209662e-05,11.3946378 -1.23209662e-05,11.75 C-0.00758042603,16.0663731 3.48367543,19.5725301 7.80004167,19.5833333 L7.81570833,19.5833333 C9.75003686,19.5882688 11.6168794,18.8726691 13.0522917,17.5760417 C16.0171492,20.2556967 20.5292675,20.2556967 23.494125,17.5760417 C26.4604562,20.2616016 30.9794188,20.2616016 33.94575,17.5760417 C36.2421905,19.6477597 39.5441143,20.1708521 42.3684437,18.9103691 C45.1927731,17.649886 47.0084685,14.8428276 47.0000295,11.75 C47.0000295,11.3946378 46.9030823,11.0460037 46.7199583,10.7414583 Z">
                                            </path>
                                            <path class="color-background"
                                                d="M39.198,22.4912623 C37.3776246,22.4928106 35.5817531,22.0149171 33.951625,21.0951667 L33.92225,21.1107282 C31.1430221,22.6838032 27.9255001,22.9318916 24.9844167,21.7998837 C24.4750389,21.605469 23.9777983,21.3722567 23.4960833,21.1018359 L23.4745417,21.1129513 C20.6961809,22.6871153 17.4786145,22.9344611 14.5386667,21.7998837 C14.029926,21.6054643 13.533337,21.3722507 13.0522917,21.1018359 C11.4250962,22.0190609 9.63246555,22.4947009 7.81570833,22.4912623 C7.16510551,22.4842162 6.51607673,22.4173045 5.875,22.2911849 L5.875,44.7220845 C5.875,45.9498589 6.7517757,46.9451667 7.83333333,46.9451667 L19.5833333,46.9451667 L19.5833333,33.6066734 L27.4166667,33.6066734 L27.4166667,46.9451667 L39.1666667,46.9451667 C40.2482243,46.9451667 41.125,45.9498589 41.125,44.7220845 L41.125,22.2822926 C40.4887822,22.4116582 39.8442868,22.4815492 39.198,22.4912623 Z">
                                            </path>
                                        </g>
                                    </g>
                                </g>
                            </g>
                        </svg>
                    </div>
                    <span class="nav-link-text ms-1">{{ __('dashboard') }}</span>
                </a>
            </li>

            {{-- Quản lý --}}
            @php
                $storiesMenuActive = request()->routeIs('admin.categories.*', 'admin.stories.*', 'admin.story-ownership.*', 'admin.comments.*',
                    'admin.chapter-reports.*', 'admin.web-feedback.*', 'admin.author-applications.*', 'admin.edit-requests.*');
            @endphp
            <li class="nav-item mt-2">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Quản lý</h6>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ $storiesMenuActive ? '' : 'collapsed' }}" data-bs-toggle="collapse"
                    href="#storiesSubmenu" role="button" aria-expanded="{{ $storiesMenuActive ? 'true' : 'false' }}"
                    aria-controls="storiesSubmenu">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-book text-dark icon-sidebar"></i>
                    </div>
                    <span class="nav-link-text ms-1">Truyện</span>
                    <i class="fas fa-chevron-down text-xs opacity-5"></i>
                </a>
                <div class="collapse mt-1 {{ $storiesMenuActive ? 'show' : '' }}" id="storiesSubmenu" style="margin-left: 15px">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
                                href="{{ route('admin.categories.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-book text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Danh sách thể loại</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.stories.*') ? 'active' : '' }}"
                                href="{{ route('admin.stories.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-layer-group text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Danh sách truyện
                                    @php
                                        $pendingStoriesCount = \App\Models\Story::where('status', 'pending')->count();
                                    @endphp
                                    @if ($pendingStoriesCount > 0)
                                        <span class="badge bg-danger ms-2">{{ $pendingStoriesCount }}</span>
                                    @endif
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}"
                                href="{{ route('admin.comments.all') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-comments text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Quản lý Bình luận
                                    @php
                                        $pendingCommentsCount = \App\Models\Comment::where('approval_status', 'pending')
                                            ->whereHas('user', function ($q) {
                                                $q->where('role', '!=', 'admin_main')->where('role', '!=', 'admin_sub');
                                            })
                                            ->whereDoesntHave('story', function ($q) {
                                                $q->whereColumn('stories.user_id', 'comments.user_id');
                                            })
                                            ->count();
                                    @endphp
                                    @if ($pendingCommentsCount > 0)
                                        <span class="badge bg-danger ms-2">{{ $pendingCommentsCount }}</span>
                                    @endif
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.chapter-reports.*') ? 'active' : '' }}"
                                href="{{ route('admin.chapter-reports.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-flag text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Báo cáo lỗi chương
                                    @php
                                        $pendingReportCount = \App\Models\ChapterReport::where('status', 'pending')->count();
                                    @endphp
                                    @if ($pendingReportCount > 0)
                                        <span class="badge bg-danger ms-2">{{ $pendingReportCount }}</span>
                                    @endif
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.web-feedback.*') ? 'active' : '' }}"
                                href="{{ route('admin.web-feedback.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-lightbulb text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Góp ý cải thiện web
                                    @php
                                        $unreadFeedbackCount = \App\Models\WebFeedback::whereNull('read_at')->count();
                                    @endphp
                                    @if ($unreadFeedbackCount > 0)
                                        <span class="badge bg-danger ms-2">{{ $unreadFeedbackCount }}</span>
                                    @endif
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.edit-requests.*') ? 'active' : '' }}"
                                href="{{ route('admin.edit-requests.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-pen-to-square text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Yêu cầu chỉnh sửa
                                    @php
                                        $pendingEditCount = \App\Models\StoryEditRequest::where('status', 'pending')->count();
                                    @endphp
                                    @if ($pendingEditCount > 0)
                                        <span class="badge bg-danger ms-2">{{ $pendingEditCount }}</span>
                                    @endif
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.author-applications.*') ? 'active' : '' }}"
                                href="{{ route('admin.author-applications.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-pen-nib text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Đơn đăng ký tác giả
                                    @php
                                        $pendingCount = \App\Models\AuthorApplication::where('status', 'pending')->count();
                                    @endphp
                                    @if ($pendingCount > 0)
                                        <span class="badge bg-danger ms-2">{{ $pendingCount }}</span>
                                    @endif
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.affiliate-links.*') ? 'active' : '' }}"
                                href="{{ route('admin.affiliate-links.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-link text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Link Affiliate (Zhihu)</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- Nạp nấm --}}
            @if (Auth::user()->role === 'admin_main')
            @php
                $depositMenuActive = request()->routeIs('admin.deposits.*', 'admin.request.payments.*',
                    'admin.banks.*', 'admin.bank-autos.*', 'admin.bank-auto-deposits.*', 'admin.paypal-deposits.*',
                    'admin.card-deposits.*', 'admin.manual-purchases.*');
            @endphp
            <li class="nav-item">
                <a class="nav-link {{ $depositMenuActive ? '' : 'collapsed' }}" data-bs-toggle="collapse"
                    href="#depositSubmenu" role="button" aria-expanded="{{ $depositMenuActive ? 'true' : 'false' }}"
                    aria-controls="depositSubmenu">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-money-bill-transfer text-dark icon-sidebar"></i>
                    </div>
                    <span class="nav-link-text ms-1">Nạp nấm</span>
                    <i class="fas fa-chevron-down text-xs opacity-5"></i>
                </a>
                <div class="collapse mt-1 {{ $depositMenuActive ? 'show' : '' }}" id="depositSubmenu" style="margin-left: 15px">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.bank-auto-deposits.*') ? 'active' : '' }}"
                                href="{{ route('admin.bank-auto-deposits.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-exchange-alt text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Nạp nấm auto</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.card-deposits.*') ? 'active' : '' }}"
                                href="{{ route('admin.card-deposits.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-credit-card text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Nạp nấm - Thẻ cào</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.paypal-deposits.*') ? 'active' : '' }}"
                                href="{{ route('admin.paypal-deposits.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fab fa-paypal text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Nạp nấm - PayPal
                                    @php
                                        $pendingPaypalCount = \App\Models\PaypalDeposit::where('status', 'processing')->count();
                                    @endphp
                                    @if ($pendingPaypalCount > 0)
                                        <span class="badge bg-danger ms-2">{{ $pendingPaypalCount }}</span>
                                    @endif
                                </span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.bank-autos.*') ? 'active' : '' }}"
                                href="{{ route('admin.bank-autos.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-robot text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Bank (Tự động)</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.deposits.*', 'admin.request.payments.*') ? 'active' : '' }}"
                                href="{{ route('admin.deposits.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-university text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Nạp nấm - Bank
                                    @php
                                        $pendingDepositsCount = \App\Models\Deposit::where('status', 'pending')->count();
                                    @endphp
                                    @if ($pendingDepositsCount > 0)
                                        <span class="badge bg-danger ms-2">{{ $pendingDepositsCount }}</span>
                                    @endif
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.banks.*') ? 'active' : '' }}"
                                href="{{ route('admin.banks.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-university text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Bank (Thủ công)</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.manual-purchases.*') ? 'active' : '' }}"
                                href="{{ route('admin.manual-purchases.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-hand-holding-dollar text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Mua thủ công</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endif

            {{-- Quản lý nấm --}}
            @if (Auth::user()->role === 'admin_main')
            @php
                $coinMenuActive = request()->routeIs('admin.coins.*', 'admin.coin.transactions', 'admin.coin-transfers.*', 'admin.coin-history.*');
            @endphp
            <li class="nav-item">
                <a class="nav-link {{ $coinMenuActive ? '' : 'collapsed' }}" data-bs-toggle="collapse"
                    href="#coinSubmenu" role="button" aria-expanded="{{ $coinMenuActive ? 'true' : 'false' }}"
                    aria-controls="coinSubmenu">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-coins text-dark icon-sidebar"></i>
                    </div>
                    <span class="nav-link-text ms-1">Quản lý nấm</span>
                    <i class="fas fa-chevron-down text-xs opacity-5"></i>
                </a>
                <div class="collapse mt-1 {{ $coinMenuActive ? 'show' : '' }}" id="coinSubmenu" style="margin-left: 15px">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.coins.*') ? 'active' : '' }}"
                                href="{{ route('admin.coins.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-coins text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Quản lý nấm</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.coin.transactions') ? 'active' : '' }}"
                                href="{{ route('admin.coin.transactions') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-history text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Kiểm soát nấm thủ công</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.coin-transfers.*') ? 'active' : '' }}"
                                href="{{ route('admin.coin-transfers.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-exchange-alt text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Giám sát chuyển nấm</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.coin-history.*') ? 'active' : '' }}"
                                href="{{ route('admin.coin-history.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-history text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Lịch sử nấm</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @elseif (Auth::user()->role === 'admin_sub')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.coin-transfers.*') ? 'active' : '' }}"
                    href="{{ route('admin.coin-transfers.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-exchange-alt text-dark icon-sidebar"></i>
                    </div>
                    <span class="nav-link-text ms-1">Chuyển nấm của tôi</span>
                </a>
            </li>
            @endif

            {{-- Người dùng --}}
            @php
                $usersMenuActive = request()->routeIs('admin.users.*', 'admin.ban-ips.*', 'admin.rate-limit.*', 'admin.notifications.*');
            @endphp
            <li class="nav-item">
                <a class="nav-link {{ $usersMenuActive ? '' : 'collapsed' }}" data-bs-toggle="collapse"
                    href="#usersSubmenu" role="button" aria-expanded="{{ $usersMenuActive ? 'true' : 'false' }}"
                    aria-controls="usersSubmenu">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-users text-dark icon-sidebar"></i>
                    </div>
                    <span class="nav-link-text ms-1">Người dùng</span>
                    <i class="fas fa-chevron-down text-xs opacity-5"></i>
                </a>
                <div class="collapse mt-1 {{ $usersMenuActive ? 'show' : '' }}" id="usersSubmenu" style="margin-left: 15px">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                                href="{{ route('admin.users.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-users text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Danh sách User</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}"
                                href="{{ route('admin.notifications.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-bell text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Gửi thông báo</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.ban-ips.*') ? 'active' : '' }}"
                                href="{{ route('admin.ban-ips.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-ban text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Ban IP</span>
                            </a>
                        </li>
                        @if (Auth::user()->role === 'admin_main')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.rate-limit.*') ? 'active' : '' }}"
                                href="{{ route('admin.rate-limit.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-gauge-high text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Rate Limit
                                    @php
                                        try {
                                            $bannedRateLimitCount = \App\Models\User::whereHas('userBan', function($q) {
                                                $q->where('rate_limit_ban', true)
                                                    ->where(function($subQ) {
                                                        $subQ->where('read', true)
                                                            ->orWhere(function($tempQ) {
                                                                $tempQ->whereNotNull('read_banned_until')
                                                                    ->where('read_banned_until', '>', now())
                                                                    ->where('read', false);
                                                            });
                                                    });
                                            })->whereIn('role', ['user', 'admin_sub'])->count();
                                        } catch (\Exception $e) {
                                            $bannedRateLimitCount = 0;
                                        }
                                    @endphp
                                    @if ($bannedRateLimitCount > 0)
                                        <span class="badge bg-danger ms-2">{{ $bannedRateLimitCount }}</span>
                                    @endif
                                </span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>

            {{-- Cài đặt --}}
            @php
                $settingsMenuActive = request()->routeIs('admin.configs.*', 'admin.sensitive-keywords.*', 'admin.logo-site.*', 'admin.banners.*',
                    'admin.daily-tasks.*', 'admin.socials.*', 'admin.guide.*');
            @endphp
            <li class="nav-item mt-2">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Cài đặt</h6>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ $settingsMenuActive ? '' : 'collapsed' }}" data-bs-toggle="collapse"
                    href="#settingsSubmenu" role="button" aria-expanded="{{ $settingsMenuActive ? 'true' : 'false' }}"
                    aria-controls="settingsSubmenu">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-gears text-dark icon-sidebar"></i>
                    </div>
                    <span class="nav-link-text ms-1">Cài đặt</span>
                    <i class="fas fa-chevron-down text-xs opacity-5"></i>
                </a>
                <div class="collapse mt-1 {{ $settingsMenuActive ? 'show' : '' }}" id="settingsSubmenu" style="margin-left: 15px">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        @if (Auth::user()->role === 'admin_main')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.configs.*') ? 'active' : '' }}"
                                href="{{ route('admin.configs.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-gears text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Cấu hình hệ thống</span>
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.sensitive-keywords.*') ? 'active' : '' }}"
                                href="{{ route('admin.sensitive-keywords.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-shield-halved text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Từ khóa nhạy cảm</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.logo-site.*') ? 'active' : '' }}"
                                href="{{ route('admin.logo-site.edit') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-regular fa-images text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Logo</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}"
                                href="{{ route('admin.banners.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-regular fa-image text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Banners</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.daily-tasks.*') ? 'active' : '' }}"
                                href="{{ route('admin.daily-tasks.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-tasks text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Quản lý nhiệm vụ</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.socials.*') ? 'active' : '' }}"
                                href="{{ route('admin.socials.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-share-nodes text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Liên hệ</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.guide.*') ? 'active' : '' }}"
                                href="{{ route('admin.guide.edit') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-book text-dark icon-sidebar"></i>
                                </div>
                                <span class="nav-link-text ms-1">Quản lý Hướng dẫn</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- Tài khoản --}}
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Tài khoản</h6>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('logout') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-right-from-bracket text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">Đăng xuất</span>
                </a>
            </li>
        </ul>
    </div>

    @push('scripts-admin')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const activeChildMenu = document.querySelector('.collapse .nav-link.active');
            if (activeChildMenu) {
                const parentCollapse = activeChildMenu.closest('.collapse');
                if (parentCollapse) {
                    parentCollapse.classList.add('show');
                    const parentLink = document.querySelector(`[href="#${parentCollapse.id}"]`);
                    if (parentLink) {
                        parentLink.classList.remove('collapsed');
                        parentLink.setAttribute('aria-expanded', 'true');
                        const chevron = parentLink.querySelector('.fa-chevron-down');
                        if (chevron) chevron.classList.add('rotated');
                    }
                }
            }

            document.addEventListener('show.bs.collapse', function(e) {
                const parentLink = document.querySelector(`[href="#${e.target.id}"]`);
                if (parentLink) {
                    parentLink.classList.remove('collapsed');
                    parentLink.setAttribute('aria-expanded', 'true');
                    const chevron = parentLink.querySelector('.fa-chevron-down');
                    if (chevron) chevron.classList.add('rotated');
                }
            });

            document.addEventListener('hide.bs.collapse', function(e) {
                const parentLink = document.querySelector(`[href="#${e.target.id}"]`);
                if (parentLink) {
                    parentLink.classList.add('collapsed');
                    parentLink.setAttribute('aria-expanded', 'false');
                    const chevron = parentLink.querySelector('.fa-chevron-down');
                    if (chevron) chevron.classList.remove('rotated');
                }
            });

            document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(el => {
                const target = document.querySelector(el.getAttribute('href'));
                if (target && target.classList.contains('show')) {
                    el.classList.remove('collapsed');
                    el.setAttribute('aria-expanded', 'true');
                    const chevron = el.querySelector('.fa-chevron-down');
                    if (chevron) chevron.classList.add('rotated');
                }
            });

            setTimeout(() => {
                const activeMenu = document.querySelector('.nav-link.active');
                if (activeMenu) {
                    const sidenav = document.getElementById('sidenav-main');
                    if (sidenav) {
                        const scrollPos = activeMenu.offsetTop - (sidenav.offsetHeight / 2) + (activeMenu.offsetHeight / 2);
                        sidenav.scrollTo({ top: Math.max(0, scrollPos), behavior: 'smooth' });
                    }
                }
            }, 300);
        });
    </script>
    <style>
        .sidenav .navbar-nav, .sidenav .navbar-nav .nav, .sidenav .collapse .nav, .sidenav .btn-toggle-nav,
        .sidenav .navbar-nav li, .sidenav .navbar-nav .nav li, .sidenav .collapse .nav li, .sidenav .btn-toggle-nav li {
            list-style: none; padding-left: 0; margin-left: 0;
        }
        .sidenav .navbar-nav li::before, .sidenav .collapse .nav li::before, .sidenav .btn-toggle-nav li::before,
        .sidenav .navbar-nav li::after, .sidenav .collapse .nav li::after, .sidenav .btn-toggle-nav li::after {
            content: none;
        }
        .sidenav .nav .nav-item .nav-link { padding-left: 1rem; padding-right: 1rem; }
        .sidenav .nav .nav-item .nav-link .icon, .sidenav .btn-toggle-nav .nav-item .nav-link .icon {
            min-width: 2rem; width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center;
        }
        .sidenav .nav-link[data-bs-toggle="collapse"] { position: relative; padding-right: 3rem !important; }
        .sidenav .nav-link[data-bs-toggle="collapse"]::after,
        .sidenav .navbar-nav .nav-link[data-bs-toggle="collapse"]::after { display: none !important; content: none !important; }
        .sidenav .nav-link[data-bs-toggle="collapse"] .fa-chevron-down {
            transition: transform 0.3s ease; position: absolute; right: 1rem; top: 50%; margin-top: -0.5em;
        }
        .sidenav .nav-link[data-bs-toggle="collapse"] .fa-chevron-down.rotated { transform: rotate(180deg); }
        .sidenav .collapse.show { display: block; }
        .sidenav .btn-toggle-nav .nav-item .nav-link {
            padding-left: 0rem; padding-right: 0rem; display: flex; align-items: center;
        }
    </style>
    @endpush
</aside>
