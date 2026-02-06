<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebFeedback;
use Illuminate\Http\Request;

class WebFeedbackController extends Controller
{
    public function index(Request $request)
    {
        $query = WebFeedback::with('user')->latest();

        if ($request->filled('read')) {
            if ($request->read === '1') {
                $query->whereNotNull('read_at');
            } else {
                $query->whereNull('read_at');
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', '%' . $search . '%')
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%' . $search . '%')->orWhere('email', 'like', '%' . $search . '%'));
            });
        }

        $feedbacks = $query->paginate(20)->withQueryString();

        return view('admin.pages.web-feedback.index', compact('feedbacks'));
    }

    public function show(WebFeedback $webFeedback)
    {
        $webFeedback->load('user');
        if (!$webFeedback->read_at) {
            $webFeedback->update(['read_at' => now()]);
        }
        return view('admin.pages.web-feedback.show', compact('webFeedback'));
    }

    public function markRead(WebFeedback $webFeedback)
    {
        $webFeedback->update(['read_at' => now()]);
        return back()->with('success', 'Đã đánh dấu đã đọc.');
    }

    public function markUnread(WebFeedback $webFeedback)
    {
        $webFeedback->update(['read_at' => null]);
        return back()->with('success', 'Đã đánh dấu chưa đọc.');
    }
}
