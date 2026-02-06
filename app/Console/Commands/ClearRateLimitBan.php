<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\RateLimitService;
use Illuminate\Console\Command;

class ClearRateLimitBan extends Command
{
    protected $signature = 'rate-limit:clear {user : User ID hoặc email}';
    protected $description = 'Gỡ block rate limit cho user (dùng khi dev local)';

    public function handle(RateLimitService $service): int
    {
        $identifier = $this->argument('user');
        $user = is_numeric($identifier)
            ? User::find($identifier)
            : User::where('email', $identifier)->first();

        if (!$user) {
            $this->error("Không tìm thấy user: {$identifier}");
            return 1;
        }

        $userBan = $user->userBan;
        if ($userBan) {
            $userBan->read = false;
            $userBan->login = false;
            $userBan->read_banned_until = null;
            $userBan->rate_limit_ban = false;
            $userBan->save();
        }

        $service->clearRateLimitCache($user);
        $this->info("Đã gỡ block rate limit cho user: {$user->email} (ID: {$user->id})");
        return 0;
    }
}
