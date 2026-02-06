<?php

namespace App\Services;

use App\Models\User;
use App\Models\Story;
use App\Models\Chapter;
use App\Models\CoinHistory;
use App\Models\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CoinService
{

    public function getPlatformFeePercentage(?User $author): int
    {
        if ($author && $author->author_fee_percentage !== null) {
            return (int) $author->author_fee_percentage;
        }
        return (int) Config::getConfig('platform_fee_percentage', 20);
    }

    public function processChapterPurchase(User $buyer, Chapter $chapter, $purchase): array
    {
        $price = $chapter->price;
        $creator = $chapter->creator ?? $chapter->story->user;
        $feePct = $this->getPlatformFeePercentage($creator);
        $netToCreator = (int) round($price * (100 - $feePct) / 100);

        DB::beginTransaction();
        try {
            $this->subtractCoins($buyer, $price, CoinHistory::TYPE_CHAPTER_PURCHASE, "Mua chương: {$chapter->title}", $purchase);
            if ($creator && $netToCreator > 0) {
                $this->addCoins($creator, $netToCreator, CoinHistory::TYPE_CHAPTER_EARNINGS, "Thu nhập chương: {$chapter->title}", $purchase);
            }
            DB::commit();
            return ['buyer_deducted' => $price, 'creator_received' => $netToCreator];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function processComboPurchase(User $buyer, Story $story, $purchase): array
    {
        $price = $story->combo_price;
        $owner = $story->user;
        $feePct = $this->getPlatformFeePercentage($owner);
        $netTotal = (int) round($price * (100 - $feePct) / 100);

        $eligibleIds = collect([$story->user_id])
            ->merge($story->coOwners()->pluck('user_id'))
            ->unique()
            ->values()
            ->filter()
            ->values();

        $paidCountByAuthor = Chapter::where('story_id', $story->id)
            ->where('status', 'published')
            ->where('price', '>', 0)
            ->whereIn('user_id', $eligibleIds)
            ->selectRaw('user_id, COUNT(*) as cnt')
            ->groupBy('user_id')
            ->pluck('cnt', 'user_id')
            ->map(fn ($v) => (int) $v);

        $nullCreatorCount = (int) Chapter::where('story_id', $story->id)
            ->where('status', 'published')
            ->where('price', '>', 0)
            ->whereNull('user_id')
            ->count();

        if ($nullCreatorCount > 0 && $story->user_id) {
            $paidCountByAuthor[$story->user_id] = ($paidCountByAuthor[$story->user_id] ?? 0) + $nullCreatorCount;
        }

        $totalPaidChapters = $paidCountByAuthor->sum();
        if ($totalPaidChapters <= 0) {
            $totalPaidChapters = 1;
            $paidCountByAuthor = collect([$story->user_id => 1]);
        }

        $distributions = [];
        $remainder = $netTotal;
        $usersToPay = [];

        foreach ($eligibleIds as $uid) {
            $cnt = $paidCountByAuthor->get($uid, 0);
            if ($cnt <= 0) continue;
            $share = (int) floor($netTotal * $cnt / $totalPaidChapters);
            $remainder -= $share;
            $usersToPay[$uid] = $share;
        }
        if ($remainder > 0 && $owner) {
            $usersToPay[$owner->id] = ($usersToPay[$owner->id] ?? 0) + $remainder;
        }

        DB::beginTransaction();
        try {
            $this->subtractCoins($buyer, $price, CoinHistory::TYPE_STORY_PURCHASE, "Mua combo truyện: {$story->title}", $purchase);
            foreach ($usersToPay as $userId => $amount) {
                if ($amount <= 0) continue;
                $recipient = User::find($userId);
                if ($recipient) {
                    $this->addCoins($recipient, $amount, CoinHistory::TYPE_STORY_EARNINGS, "Thu nhập combo: {$story->title}", $purchase);
                    $distributions[$userId] = $amount;
                }
            }
            DB::commit();
            return ['buyer_deducted' => $price, 'distributions' => $distributions];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Add coins to user with transaction record
     */
    public function addCoins(User $user, int $amount, string $transactionType, string $description = null, $reference = null, $adminId = null)
    {
        return $this->processTransaction($user, $amount, 'add', $transactionType, $description, $reference, $adminId);
    }

    /**
     * Subtract coins from user with transaction record
     */
    public function subtractCoins(User $user, int $amount, string $transactionType, string $description = null, $reference = null, $adminId = null)
    {
        if ($user->coins < $amount) {
            throw new \Exception('Không đủ nấm để thực hiện giao dịch này');
        }

        return $this->processTransaction($user, -$amount, 'subtract', $transactionType, $description, $reference, $adminId);
    }

    /**
     * Process coin transaction with full logging
     */
    protected function processTransaction(User $user, int $amount, string $type, string $transactionType, string $description = null, $reference = null, $adminId = null)
    {
        DB::beginTransaction();

        try {
            $balanceBefore = $user->coins;
            $balanceAfter = $balanceBefore + $amount;

            $user->coins = $balanceAfter;
            $user->save();

            $transaction = CoinHistory::create([
                'user_id' => $user->id,
                'amount' => abs($amount),
                'type' => $type,
                'transaction_type' => $transactionType,
                'description' => $description,
                'reference_id' => $reference ? $reference->id : null,
                'reference_type' => $reference ? get_class($reference) : null,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();
            return $transaction;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Coin transaction failed', [
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => $type,
                'transaction_type' => $transactionType,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Transfer coins between users (for purchases)
     */
    public function transferCoins(User $fromUser, User $toUser, int $amount, string $transactionType, string $description = null, $reference = null)
    {
        DB::beginTransaction();

        try {
            // Subtract from buyer
            $buyerTransaction = $this->subtractCoins($fromUser, $amount, $transactionType, $description, $reference);

            // // Add to seller
            // $sellerTransaction = $this->addCoins($toUser, $amount, $this->getEarningsType($transactionType), $description, $reference);

            DB::commit();

            return [
                'buyer_transaction' => $buyerTransaction
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get earnings transaction type
     */
    protected function getEarningsType($purchaseType)
    {
        $mapping = [
            CoinHistory::TYPE_CHAPTER_PURCHASE => CoinHistory::TYPE_CHAPTER_EARNINGS,
            CoinHistory::TYPE_STORY_PURCHASE => CoinHistory::TYPE_STORY_EARNINGS,
        ];

        return $mapping[$purchaseType] ?? 'earnings';
    }

    /**
     * Get user transaction history
     */
    public function getUserTransactions(User $user, $filters = [])
    {
        $query = $user->coinHistories()->with(['reference']);

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['transaction_type'])) {
            $query->where('transaction_type', $filters['transaction_type']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($filters['per_page'] ?? 20);
    }

    /**
     * Get transaction statistics for user
     */
    public function getUserStats(User $user, $dateFrom = null, $dateTo = null)
    {
        $query = $user->coinHistories();

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return [
            'total_added' => (clone $query)->where('type', 'add')->sum('amount'),
            'total_subtracted' => (clone $query)->where('type', 'subtract')->sum('amount'),
            'total_transactions' => (clone $query)->count(),
            'by_type' => (clone $query)->selectRaw('transaction_type, type, SUM(amount) as total, COUNT(*) as count')
                ->groupBy('transaction_type', 'type')
                ->get(),
        ];
    }

    /**
     * Get admin transaction statistics
     */
    public function getAdminStats($dateFrom = null, $dateTo = null)
    {
        $query = CoinHistory::query();

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Tính nấm nhiệm vụ từ user_daily_tasks
        $dailyTaskQuery = \App\Models\UserDailyTask::query();
        if ($dateFrom) {
            $dailyTaskQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $dailyTaskQuery->whereDate('created_at', '<=', $dateTo);
        }
        $totalDailyTaskCoins = $dailyTaskQuery->sum(DB::raw('coin_reward * completed_count'));

        return [
            'total_added' => (clone $query)->where('type', 'add')->sum('amount'),
            'total_subtracted' => (clone $query)->where('type', 'subtract')->sum('amount'),
            'total_transactions' => (clone $query)->count(),
            'total_daily_task_coins' => $totalDailyTaskCoins ?? 0,
            'by_type' => (clone $query)->selectRaw('transaction_type, type, SUM(amount) as total, COUNT(*) as count')
                ->groupBy('transaction_type', 'type')
                ->get(),
            'by_user' => (clone $query)->with('user')
                ->selectRaw('user_id, type, SUM(amount) as total, COUNT(*) as count')
                ->groupBy('user_id', 'type')
                ->get(),
        ];
    }
}
