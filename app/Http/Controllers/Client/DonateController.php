<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Config;
use App\Models\Donate;
use App\Services\CoinService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DonateController extends Controller
{
    protected $coinService;

    public function __construct(CoinService $coinService)
    {
        $this->coinService = $coinService;
    }

    /**
     * Display donate page
     */
    public function index()
    {
        $donateFeePercentage = Config::getConfig('donate_fee_percentage', 10);
        return view('pages.information.user.donate', compact('donateFeePercentage'));
    }

    /**
     * Search users by name or email
     */
    public function searchUser(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2|max:255',
        ], [
            'query.required' => 'Vui lòng nhập tên hoặc email để tìm kiếm.',
            'query.min' => 'Từ khóa tìm kiếm phải có ít nhất 2 ký tự.',
            'query.max' => 'Từ khóa tìm kiếm không được vượt quá 255 ký tự.',
        ]);

        $query = $request->input('query');
        $currentUser = Auth::user();
        $currentUserId = $currentUser->id;

        $usersQuery = User::where('id', '!=', $currentUserId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            });

        if ($currentUser->role === User::ROLE_USER) {
            $usersQuery->whereIn('role', [User::ROLE_AUTHOR]);
        }
        if ($currentUser->role === User::ROLE_AUTHOR) {
            $usersQuery->where('active', true);
        }

        $users = $usersQuery->select('id', 'name', 'email', 'avatar', 'role')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'users' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar ? Storage::url($user->avatar) : null,
                    'role' => $user->role,
                    'role_label' => $user->role === 'author' ? 'Dịch giả' : 'Người dùng',
                ];
            }),
        ]);
    }

    /**
     * Process donate transaction
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'amount' => 'required|integer|min:1',
            'message' => 'nullable|string|max:100',
        ], [
            'recipient_id.required' => 'Vui lòng chọn người nhận.',
            'recipient_id.exists' => 'Người nhận không tồn tại.',
            'amount.required' => 'Vui lòng nhập số cám muốn donate.',
            'amount.integer' => 'Số cám phải là số nguyên.',
            'amount.min' => 'Số cám phải lớn hơn 0.',
            'message.max' => 'Lời nhắn không được vượt quá 100 ký tự.',
        ], [
            'recipient_id' => 'Người nhận',
            'amount' => 'Số cám',
            'message' => 'Lời nhắn',
        ]);

        $sender = Auth::user();
        $recipient = User::findOrFail($request->recipient_id);

        if ($sender->id === $recipient->id) {
            return redirect()->back()->with('error', 'Bạn không thể donate cho chính mình.');
        }

        if ($sender->role === User::ROLE_USER && !in_array($recipient->role, [User::ROLE_AUTHOR])) {
            return redirect()->back()->with('error', 'Bạn chỉ có thể donate cho dịch giả.');
        }

        if ($sender->role === User::ROLE_AUTHOR && !$recipient->active) {
            return redirect()->back()->with('error', 'Không thể donate cho tài khoản chưa kích hoạt.');
        }

        $donateAmount = $request->amount;
        if ($sender->coins < $donateAmount) {
            return redirect()->back()->with('error', 'Bạn không đủ cám để donate.');
        }

        $donateFeePercentage = Config::getConfig('donate_fee_percentage', 10);
        $feeAmount = (int) floor($donateAmount * $donateFeePercentage / 100);
        $receivedAmount = $donateAmount - $feeAmount;

        DB::beginTransaction();

        try {
            $senderDescription = "Donate {$donateAmount} cám cho {$recipient->name}" . ($request->message ? " - {$request->message}" : '');
            $senderTransaction = $this->coinService->subtractCoins(
                $sender,
                $donateAmount,
                \App\Models\CoinHistory::TYPE_DONATE_SENT,
                $senderDescription
            );

            $recipientDescription = "Nhận donate {$donateAmount} cám từ {$sender->name}" . ($feeAmount > 0 ? " (phí {$donateFeePercentage}%: -{$feeAmount} cám, nhận: {$receivedAmount} cám)" : '') . ($request->message ? " - {$request->message}" : '');
            $this->coinService->addCoins(
                $recipient,
                $receivedAmount,
                \App\Models\CoinHistory::TYPE_DONATE_RECEIVED,
                $recipientDescription
            );

            Donate::create([
                'sender_id' => $sender->id,
                'recipient_id' => $recipient->id,
                'amount' => $donateAmount,
                'fee_percentage' => $donateFeePercentage,
                'fee_amount' => $feeAmount,
                'received_amount' => $receivedAmount,
                'message' => $request->message,
            ]);

            DB::commit();

            return redirect()->route('user.donate')->with('success', "Đã donate {$donateAmount} cám cho {$recipient->name}. Người nhận nhận được {$receivedAmount} cám" . ($feeAmount > 0 ? " (phí {$donateFeePercentage}%: {$feeAmount} cám)" : '') . '.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Donate transaction failed', [
                'sender_id' => $sender->id,
                'recipient_id' => $recipient->id,
                'amount' => $donateAmount,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Có lỗi xảy ra khi thực hiện donate. Vui lòng thử lại.')->withInput();
        }
    }

    /**
     * Lịch sử donate (gửi + nhận) từ model Donate
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $filterType = $request->get('type', '');
        $query = Donate::with(['sender', 'recipient'])
            ->where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)->orWhere('recipient_id', $user->id);
            });
        if ($filterType === 'sent') {
            $query->where('sender_id', $user->id);
        } elseif ($filterType === 'received') {
            $query->where('recipient_id', $user->id);
        }
        $donates = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        return view('pages.information.user.donate_history', compact('donates', 'filterType'));
    }
}
