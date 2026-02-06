<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateLink;
use App\Models\AffiliateLinkClick;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AffiliateLinkController extends Controller
{
    public function index()
    {
        $links = AffiliateLink::withCount('clicks')->orderByDesc('created_at')->paginate(15);
        return view('admin.pages.affiliate-links.index', compact('links'));
    }

    public function stats(Request $request)
    {
        $totalClicks = AffiliateLinkClick::count();
        $byLink = AffiliateLink::withCount('clicks')
            ->orderByDesc('clicks_count')
            ->paginate(15, ['*'], 'link_page')
            ->withQueryString();
        $storyPage = $request->get('story_page', 1);
        $storyPerPage = 15;
        $byStoryQuery = AffiliateLinkClick::selectRaw('story_id, count(*) as clicks_count')
            ->groupBy('story_id')
            ->orderByDesc('clicks_count');
        $totalStories = DB::table(DB::raw("({$byStoryQuery->toSql()}) as sub"))
            ->mergeBindings($byStoryQuery->getQuery())->count();
        $byStoryRows = $byStoryQuery->offset(($storyPage - 1) * $storyPerPage)->limit($storyPerPage)->get();
        $byStoryRows = new LengthAwarePaginator(
            $byStoryRows,
            $totalStories,
            $storyPerPage,
            $storyPage,
            ['path' => $request->url(), 'pageName' => 'story_page']
        );
        $byStoryRows->withQueryString();
        $stories = \App\Models\Story::whereIn('id', $byStoryRows->pluck('story_id'))->get()->keyBy('id');
        return view('admin.pages.affiliate-links.stats', compact('totalClicks', 'byLink', 'byStoryRows', 'stories'));
    }

    public function create()
    {
        return view('admin.pages.affiliate-links.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'url' => 'required|url|max:500',
            'title' => 'nullable|string|max:255',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ],[
            'url.required' => 'URL là bắt buộc',
            'url.url' => 'URL không hợp lệ',
            'url.max' => 'URL không được vượt quá 500 ký tự',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự',
            'is_active.boolean' => 'Trạng thái không hợp lệ',
            'banner.image' => 'Ảnh banner phải là ảnh',
            'banner.mimes' => 'Ảnh banner phải là ảnh JPEG, PNG, JPG, GIF',
            'banner.max' => 'Ảnh banner không được vượt quá 2MB',
        ]);

        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('banner')) {
            $data['banner_path'] = $request->file('banner')->store('affiliate-banners', 'public');
        }

        AffiliateLink::create($data);

        return redirect()->route('admin.affiliate-links.index')->with('success', 'Đã thêm link affiliate.');
    }

    public function edit(AffiliateLink $affiliateLink)
    {
        return view('admin.pages.affiliate-links.edit', compact('affiliateLink'));
    }

    public function update(Request $request, AffiliateLink $affiliateLink)
    {
        $data = $request->validate([
            'url' => 'required|url|max:500',
            'title' => 'nullable|string|max:255',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ],[
            'url.required' => 'URL là bắt buộc',
            'url.url' => 'URL không hợp lệ',
            'url.max' => 'URL không được vượt quá 500 ký tự',
            'title.string' => 'Tiêu đề phải là chuỗi',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự',
            'is_active.boolean' => 'Trạng thái không hợp lệ',
            'banner.image' => 'Ảnh banner phải là ảnh',
            'banner.mimes' => 'Ảnh banner phải là ảnh JPEG, PNG, JPG, GIF',
            'banner.max' => 'Ảnh banner không được vượt quá 2MB',
        ]);

        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('banner')) {
            if ($affiliateLink->banner_path) {
                Storage::disk('public')->delete($affiliateLink->banner_path);
            }
            $data['banner_path'] = $request->file('banner')->store('affiliate-banners', 'public');
        }

        $affiliateLink->update($data);

        return redirect()->route('admin.affiliate-links.index')->with('success', 'Đã cập nhật link.');
    }

    public function destroy(AffiliateLink $affiliateLink)
    {
        if ($affiliateLink->banner_path) {
            Storage::disk('public')->delete($affiliateLink->banner_path);
        }
        $affiliateLink->delete();
        return redirect()->back()->with('success', 'Đã xóa link.');
    }
}
