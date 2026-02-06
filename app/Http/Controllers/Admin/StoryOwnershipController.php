<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Models\StoryCoOwner;
use App\Models\StoryOwnershipTransfer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StoryOwnershipController extends Controller
{
    /**
     * Show ownership management page for a story
     */
    public function show(Story $story)
    {
        $story->load(['user', 'editor', 'coOwners.user', 'ownershipTransfers' => fn($q) => $q->with(['fromUser', 'toUser', 'transferredBy', 'affectedUser'])->latest()->limit(20)]);

        $authors = User::whereIn('role', ['author'])
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.pages.story-ownership.show', compact('story', 'authors'));
    }

    /**
     * Transfer story ownership to another user
     */
    public function transfer(Request $request, Story $story)
    {
        $request->validate([
            'new_owner_id' => ['required', 'exists:users,id', Rule::notIn([$story->user_id])],
            'note' => 'nullable|string|max:1000',
        ], [
            'new_owner_id.required' => 'Vui lòng chọn chủ sở hữu mới.',
            'new_owner_id.not_in' => 'Chủ sở hữu mới phải khác chủ hiện tại.',
        ]);

        $oldOwner = $story->user;
        $newOwner = User::findOrFail($request->new_owner_id);

        DB::beginTransaction();
        try {
            if ($oldOwner && !StoryCoOwner::where('story_id', $story->id)->where('user_id', $oldOwner->id)->exists()) {
                StoryCoOwner::create([
                    'story_id' => $story->id,
                    'user_id' => $oldOwner->id,
                ]);
            }

            $story->update([
                'user_id' => $newOwner->id,
                'editor_id' => $newOwner->id,
            ]);

            StoryOwnershipTransfer::create([
                'story_id' => $story->id,
                'from_user_id' => $oldOwner?->id,
                'to_user_id' => $newOwner->id,
                'transferred_by_id' => Auth::id(),
                'transfer_type' => StoryOwnershipTransfer::TYPE_OWNERSHIP_CHANGE,
                'note' => $request->note,
            ]);

            DB::commit();

            return redirect()->route('admin.story-ownership.show', $story)
                ->with('success', "Đã chuyển quyền sở hữu sang {$newOwner->name}. {$oldOwner->name} đã được thêm vào đồng sở hữu.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    /**
     * Add co-owner
     */
    public function addCoOwner(Request $request, Story $story)
    {
        $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::notIn([$story->user_id]),
                Rule::unique('story_co_owners', 'user_id')->where('story_id', $story->id),
            ],
        ], [
            'user_id.required' => 'Vui lòng chọn tác giả.',
            'user_id.not_in' => 'Chủ sở hữu hiện tại không cần thêm vào đồng sở hữu.',
            'user_id.unique' => 'Tác giả này đã là đồng sở hữu.',
        ]);

        $user = User::findOrFail($request->user_id);

        DB::beginTransaction();
        try {
            StoryCoOwner::create([
                'story_id' => $story->id,
                'user_id' => $user->id,
            ]);

            StoryOwnershipTransfer::create([
                'story_id' => $story->id,
                'from_user_id' => null,
                'to_user_id' => null,
                'transferred_by_id' => Auth::id(),
                'transfer_type' => StoryOwnershipTransfer::TYPE_CO_OWNER_ADDED,
                'affected_user_id' => $user->id,
                'note' => "Thêm {$user->name} làm đồng sở hữu",
            ]);

            DB::commit();

            return redirect()->route('admin.story-ownership.show', $story)
                ->with('success', "Đã thêm {$user->name} làm đồng sở hữu");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove co-owner
     */
    public function removeCoOwner(Story $story, StoryCoOwner $coOwner)
    {
        if ($coOwner->story_id !== $story->id) {
            abort(404);
        }

        if ($coOwner->user_id == $story->editor_id) {
            return back()->withErrors(['error' => 'Không thể xóa biên tập viên hiện tại khỏi danh sách đồng sở hữu.']);
        }

        $user = $coOwner->user;

        DB::beginTransaction();
        try {
            $coOwner->delete();

            StoryOwnershipTransfer::create([
                'story_id' => $story->id,
                'from_user_id' => null,
                'to_user_id' => null,
                'transferred_by_id' => Auth::id(),
                'transfer_type' => StoryOwnershipTransfer::TYPE_CO_OWNER_REMOVED,
                'affected_user_id' => $user->id,
                'note' => "Xóa {$user->name} khỏi đồng sở hữu",
            ]);

            DB::commit();

            return redirect()->route('admin.story-ownership.show', $story)
                ->with('success', "Đã xóa {$user->name} khỏi đồng sở hữu");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }
}
