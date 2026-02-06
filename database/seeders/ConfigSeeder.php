<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Config;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Config::setConfig(
            'coin_exchange_rate',
            10,
            'quy đổi tiền sang cám 100 VND = 1 cám'
        );

        Config::setConfig('bonus_base_amount', 100000, 'Mốc số tiền đầu tiên để tính thưởng');
        Config::setConfig('bonus_base_cam',    300,    'Cám tặng tại mốc base_amount');

        Config::setConfig('bonus_double_amount', 200000, 'Mốc số tiền thứ 2');
        Config::setConfig('bonus_double_cam',    1000,   'Cám tặng tại mốc double_amount');

        Config::setConfig(
            'coin_bank_percentage',
            0,
            'Phí chuyển khoản ngân hàng thủ công (15%)'
        );

        Config::setConfig(
            'coin_bank_auto_percentage',
            0,
            'Phí nạp ngân hàng tự động (0%) nếu có nhập thì mới tính'
        );

        Config::setConfig(
            'coin_paypal_rate',
            20000,
            'quy đổi 1 đô sang bao nhiêu tiền việt'
        );

        Config::setConfig(
            'coin_paypal_percentage',
            0,
            'Phí nạp paypal (0%) nếu có nhập thì mới tính'
        );

        Config::setConfig(
            'coin_card_percentage',
            20,
            'Phí nạp thẻ (%)'
        );

        Config::setConfig(
            'card_wrong_amount_penalty',
            50,
            'Số tiền phạt nếu người dùng nhập sai số tiền rút (50% = trừ 50% giá trị thẻ thực)'
        );

        Config::setConfig(
            'daily_task_login_reward',
            10,
            'Số cám thưởng khi hoàn thành nhiệm vụ đăng nhập hàng ngày'
        );

        Config::setConfig(
            'daily_task_comment_reward',
            10,
            'Số cám thưởng khi hoàn thành nhiệm vụ bình luận truyện'
        );

        Config::setConfig(
            'daily_task_bookmark_reward',
            10,
            'Số cám thưởng khi hoàn thành nhiệm vụ theo dõi truyện'
        );

        Config::setConfig(
            'paypal_me_link',
            'https://www.paypal.com/paypalme/minhnguyen231',
            'Link PayPal.me để nhận thanh toán'
        );

        Config::setConfig(
            'author_max_incomplete_stories',
            0,
            'Số truyện chưa hoàn thành tối đa mỗi tác giả có thể đăng. 0 = không giới hạn.'
        );

        Config::setConfig(
            'zhihu_aff_interval_minutes',
            1440,
            'Khoảng thời gian (phút) giữa mỗi lần hiện quảng cáo affiliate trên truyện zhihu. 1440 = 1 lần/ngày.'
        );

        Config::setConfig(
            'donate_fee_percentage',
            10,
            'Phí donate (%) - phí này sẽ được trừ từ số tiền donate. Ví dụ: donate 100 cám với phí 10% thì người nhận nhận 90 cám.'
        );

        Config::setConfig(
            'admin_sub_can_approve_stories',
            0,
            'Cho phép admin_sub duyệt truyện (1=bật, 0=tắt). Chỉ admin_main mới xem và chỉnh config này.'
        );

        Config::setConfig(
            'platform_fee_percentage',
            20,
            'Phí nền tảng mặc định (%) - trừ từ doanh thu mua chương/combo trước khi chia cho tác giả. Tác giả có thể có phí riêng trong author_fee_percentage.'
        );

        Config::setConfig(
            'facebook_page_url',
            'https://www.facebook.com/profile.php?id=61572454674711',
            'URL fan page Facebook (dùng cho footer, trang chương, trang nạp cám)'
        );
    }
}
