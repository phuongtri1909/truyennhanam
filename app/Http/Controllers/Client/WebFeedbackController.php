<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\WebFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebFeedbackController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        return view('pages.information.user.web_feedback');
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|min:10|max:2000',
            'intensity_level' => 'required|in:low,medium,high,urgent',
        ], [
            'content.required' => 'Nội dung góp ý không được để trống.',
            'content.min' => 'Nội dung tối thiểu 10 ký tự.',
            'content.max' => 'Nội dung tối đa 2000 ký tự.',
            'intensity_level.required' => 'Vui lòng chọn mức độ mong muốn cải thiện.',
            'intensity_level.in' => 'Mức độ không hợp lệ.',
        ]);

        WebFeedback::create([
            'user_id' => Auth::id(),
            'content' => $request->content,
            'intensity_level' => $request->intensity_level,
        ]);

        return redirect()->route('user.feedback.create')
            ->with('success', 'Cảm ơn bạn đã góp ý! Chúng mình sẽ xem và cải thiện.');
    }
}
