<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Story;
use App\Models\StoryEditRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class StoryEditRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = StoryEditRequest::with(['story', 'user'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('story', fn($sq) => $sq->where('title', 'like', "%{$search}%"))
                    ->orWhereHas('user', fn($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        $editRequests = $query->paginate(15);
        $editRequests->appends($request->only(['status', 'search']));

        $pendingCount = StoryEditRequest::where('status', 'pending')->count();
        $approvedCount = StoryEditRequest::where('status', 'approved')->count();
        $rejectedCount = StoryEditRequest::where('status', 'rejected')->count();

        return view('admin.pages.edit-requests.index', compact(
            'editRequests',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }

    public function show(StoryEditRequest $editRequest)
    {
        $editRequest->load(['story', 'story.categories']);
        $story = $editRequest->story;
        $categoryIds = $editRequest->category_ids;

        return view('admin.pages.edit-requests.show', compact('editRequest', 'story', 'categoryIds'));
    }

    public function approve(Request $request, StoryEditRequest $editRequest)
    {
        $request->validate(['admin_note' => 'nullable|string|max:1000']);

        if ($editRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Yêu cầu này đã được xử lý.');
        }

        $story = $editRequest->story;

        DB::beginTransaction();
        try {
            $oldCovers = null;
            $useNewCover = !empty($editRequest->cover) && $editRequest->cover !== $story->cover;

            if ($useNewCover) {
                $oldCovers = array_filter([$story->cover, $story->cover_thumbnail]);
            }

            $story->update([
                'title' => $editRequest->title,
                'slug' => $editRequest->slug,
                'description' => $editRequest->description,
                'author_name' => $editRequest->author_name ?? $story->author_name,
                'cover' => $editRequest->cover ?? $story->cover,
                'cover_thumbnail' => $editRequest->cover_thumbnail ?? $story->cover_thumbnail,
                'is_18_plus' => $editRequest->is_18_plus ?? $story->is_18_plus,
                'has_combo' => $editRequest->has_combo ?? $story->has_combo,
                'combo_price' => $editRequest->combo_price ?? $story->combo_price,
            ]);

            if (!empty($editRequest->category_ids)) {
                $story->categories()->sync($editRequest->category_ids);
            }

            $editRequest->update([
                'status' => 'approved',
                'admin_note' => $request->admin_note,
                'reviewed_at' => Carbon::now(),
            ]);

            DB::commit();

            if ($oldCovers && $useNewCover) {
                Storage::disk('public')->delete($oldCovers);
            }

            return redirect()->route('admin.edit-requests.index')
                ->with('success', 'Đã phê duyệt và áp dụng thay đổi.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving edit request: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi phê duyệt.');
        }
    }

    public function reject(Request $request, StoryEditRequest $editRequest)
    {
        $request->validate([
            'admin_note' => 'required|string|max:1000',
        ], [
            'admin_note.required' => 'Vui lòng nhập lý do từ chối.',
        ]);

        if ($editRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Yêu cầu này đã được xử lý.');
        }

        DB::beginTransaction();
        try {
            $editRequest->update([
                'status' => 'rejected',
                'admin_note' => $request->admin_note,
                'reviewed_at' => Carbon::now(),
            ]);

            if ($editRequest->cover && $editRequest->cover !== $editRequest->story->cover) {
                Storage::disk('public')->delete(array_filter([
                    $editRequest->cover,
                    $editRequest->cover_thumbnail,
                ]));
            }

            DB::commit();

            return redirect()->route('admin.edit-requests.index')
                ->with('success', 'Đã từ chối yêu cầu chỉnh sửa.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting edit request: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi từ chối.');
        }
    }
}
