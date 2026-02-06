<?php

namespace App\Http\Controllers\Admin;

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
    public function listApplications(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin_main' && Auth::user()->role !== 'admin_sub') {
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang này.');
        }
        
        $query = AuthorApplication::with('user')->latest();
            
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending');
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $applications = $query->paginate(10);
        
        $pendingCount = AuthorApplication::where('status', 'pending')->count();
        $approvedCount = AuthorApplication::where('status', 'approved')->count();
        $rejectedCount = AuthorApplication::where('status', 'rejected')->count();
        
        return view('admin.pages.author-applications.index', compact(
            'applications',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }
    
    public function showApplication(AuthorApplication $application)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin_main' && Auth::user()->role !== 'admin_sub') {
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang này.');
        }
        
        return view('admin.pages.author-applications.show', compact('application'));
    }
    
    public function approveApplication(Request $request, AuthorApplication $application)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin_main' && Auth::user()->role !== 'admin_sub') {
            return redirect()->route('home')->with('error', 'Bạn không có quyền thực hiện hành động này.');
        }
        
        $request->validate([
            'admin_note' => 'nullable|string|max:1000',
        ]);
        
        if ($application->status !== 'pending') {
            return redirect()->back()->with('error', 'Đơn đăng ký này đã được xử lý.');
        }
        
        DB::beginTransaction();
        try {
            $application->update([
                'status' => AuthorApplication::STATUS_APPROVED,
                'admin_note' => $request->admin_note,
                'reviewed_at' => Carbon::now(),
            ]);
            
            $application->user->update([
                'role' => 'author'
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.author-applications.index')
                ->with('success', 'Đơn đăng ký đã được phê duyệt và người dùng đã được nâng cấp thành tác giả.');
        } catch (\Exception $e) {
            Log::error('Error approving author application: ' . $e->getMessage());
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi phê duyệt đơn đăng ký');
        }
    }
    
    public function rejectApplication(Request $request, AuthorApplication $application)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin_main' && Auth::user()->role !== 'admin_sub') {
            return redirect()->route('home')->with('error', 'Bạn không có quyền thực hiện hành động này.');
        }
        
        $request->validate([
            'admin_note' => 'required|string|max:1000',
        ], [
            'admin_note.required' => 'Vui lòng cung cấp lý do từ chối đơn đăng ký.'
        ]);
        
        if ($application->status !== 'pending') {
            return redirect()->back()->with('error', 'Đơn đăng ký này đã được xử lý.');
        }
        
        try {
            $application->update([
                'status' => AuthorApplication::STATUS_REJECTED,
                'admin_note' => $request->admin_note,
                'reviewed_at' => Carbon::now(),
            ]);
            
            return redirect()->route('admin.author-applications.index')
                ->with('success', 'Đơn đăng ký đã bị từ chối.');
        } catch (\Exception $e) {
            Log::error('Error rejecting author application: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi từ chối đơn đăng ký. Vui lòng thử lại sau.');
        }
    }
}
