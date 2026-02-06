<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SensitiveKeyword;
use Illuminate\Http\Request;

class SensitiveKeywordController extends Controller
{
    public function index()
    {
        $keywords = SensitiveKeyword::orderBy('keyword')->get();
        return view('admin.pages.sensitive-keywords.index', compact('keywords'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string|max:255',
        ], [
            'keyword.required' => 'Vui lòng nhập từ khóa.',
            'keyword.max' => 'Từ khóa không được quá 255 ký tự.',
        ]);

        $keyword = trim($request->keyword);
        if (empty($keyword)) {
            return redirect()->route('admin.sensitive-keywords.index')
                ->with('error', 'Từ khóa không được để trống.');
        }

        if (SensitiveKeyword::whereRaw('LOWER(keyword) = ?', [mb_strtolower($keyword)])->exists()) {
            return redirect()->route('admin.sensitive-keywords.index')
                ->with('error', 'Từ khóa này đã tồn tại.');
        }

        SensitiveKeyword::create(['keyword' => $keyword]);

        return redirect()->route('admin.sensitive-keywords.index')
            ->with('success', 'Đã thêm từ khóa.');
    }

    public function destroy(SensitiveKeyword $sensitiveKeyword)
    {
        $sensitiveKeyword->delete();
        return redirect()->route('admin.sensitive-keywords.index')
            ->with('success', 'Đã xóa từ khóa.');
    }
}
