<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\AuthorApplication;
use App\Services\TelegramService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AuthorApplicationController extends Controller
{
    public function showApplicationForm()
    {
        $user = Auth::user();
        
        if ($user->role === 'author' || $user->role === 'admin') {
            return redirect()->route('author.index');
        }
        
        $application = $user->latestAuthorApplication();
        
        return view('pages.author.author_application', compact('user', 'application'));
    }
    
    public function submitApplication(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role === 'author' || $user->role === 'admin') {
            return redirect()->route('author.index')
                ->with('error', 'Bạn đã là tác giả, không cần đăng ký.');
        }
        
        if ($user->hasPendingAuthorApplication()) {
            return redirect()->back()
                ->with('error', 'Bạn đã có một đơn đăng ký đang chờ xét duyệt.');
        }
        
        $request->validate([
            'facebook_link' => 'required|url|max:255',
            'telegram_link' => 'nullable|url|max:255',
            'other_platform' => 'required|string|max:100',
            'other_platform_link' => 'required|url|max:255',
            'introduction' => 'nullable|string|min:50|max:1000',
        ], [
            'facebook_link.required' => 'Link Facebook là bắt buộc.',
            'facebook_link.url' => 'Link Facebook không hợp lệ.',
            'facebook_link.max' => 'Link Facebook không được vượt quá 255 ký tự.',
            'telegram_link.url' => 'Link Telegram không hợp lệ.',
            'telegram_link.max' => 'Link Telegram không được vượt quá 255 ký tự.',
            'other_platform.required' => 'Tên nền tảng khác là bắt buộc.',
            'other_platform.max' => 'Tên nền tảng khác không được vượt quá 100 ký tự.',
            'other_platform_link.required' => 'Link các nền tảng khác là bắt buộc.',
            'other_platform_link.url' => 'Link các nền tảng khác không hợp lệ.',
            'other_platform_link.max' => 'Link các nền tảng khác không được vượt quá 255 ký tự.',
            'introduction.min' => 'Phần giới thiệu phải có ít nhất 50 ký tự.',
            'introduction.max' => 'Phần giới thiệu không được vượt quá 1000 ký tự.',
        ]);
        
        try {
            $application = AuthorApplication::create([
                'user_id' => $user->id,
                'facebook_link' => $request->facebook_link,
                'telegram_link' => $request->telegram_link,
                'other_platform' => $request->other_platform,
                'other_platform_link' => $request->other_platform_link,
                'introduction' => $request->introduction,
                'status' => AuthorApplication::STATUS_PENDING,
                'submitted_at' => now(),
            ]);
            
            try {
                TelegramService::notifyNewAuthorApplication($application);
            } catch (\Exception $telegramError) {
                Log::error('Failed to send Telegram notification: ' . $telegramError->getMessage());
            }
            
            return redirect()->route('author.application')
                ->with('success', 'Đơn đăng ký của bạn đã được gửi thành công và đang chờ xét duyệt.');
        } catch (\Exception $e) {
            Log::error('Error submitting author application: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau.')
                ->withInput();
        }
    }
}
