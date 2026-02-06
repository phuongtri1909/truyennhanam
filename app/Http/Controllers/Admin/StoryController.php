<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Story;
use App\Models\Config;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\StorySubmit;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class StoryController extends Controller
{

    private function processAndSaveImage($imageFile)
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "{$timestamp}_{$randomString}";

       
        Storage::disk('public')->makeDirectory("covers/{$yearMonth}/original");
        Storage::disk('public')->makeDirectory("covers/{$yearMonth}/thumbnail");

       
        $originalImage = Image::make($imageFile);
        $originalImage->encode('webp', 90);
        Storage::disk('public')->put(
            "covers/{$yearMonth}/original/{$fileName}.webp",
            $originalImage->stream()
        );

       
        $originalImageJpeg = Image::make($imageFile);
        $originalImageJpeg->encode('jpg', 70);
        Storage::disk('public')->put(
            "covers/{$yearMonth}/thumbnail/{$fileName}.jpg",
            $originalImageJpeg->stream()
        );

        return [
            'original' => "covers/{$yearMonth}/original/{$fileName}.webp",
            'thumbnail' => "covers/{$yearMonth}/thumbnail/{$fileName}.jpg"
        ];
    }

    public function index(Request $request)
    {
        $query = Story::with(['user', 'categories', 'editor:id,name'])
            ->withCount('chapters');

        $totalStories = Story::count();
        $publishedStories = Story::where('status', 'published')->count();
        $draftStories = Story::where('status', 'draft')->count();
        $pendingStories = Story::where('status', 'pending')->count();
        $rejectedStories = Story::where('status', 'rejected')->count();
        $featuredStories = Story::where('is_featured', true)->count();
        $hiddenStories = Story::where('hide', true)->count();

        // Apply status filter
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if (in_array($request->get('hide'), ['0', '1'], true)) {
            $query->where('hide', (bool) $request->hide);
        }

        // Apply category filter
        if ($request->category) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }

       
        if ($request->featured !== null && $request->featured !== '') {
            $query->where('is_featured', (bool) $request->featured);
        }

        // Apply search filter
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('author_name', 'like', "%{$search}%")
                    ->orWhereHas('keywords', fn ($kw) => $kw->where('keyword', 'like', "%{$search}%"));
            });
        }

       
        $query->orderBy('is_featured', 'desc')
            ->orderBy('featured_order', 'desc')
            ->orderBy('created_at', 'desc');

        $stories = $query->paginate(15);

       
        if ($request->hasAny(['status', 'category', 'search', 'featured'])) {
            $stories->appends($request->only(['status', 'category', 'search', 'featured']));
        }

       
        $categories = Category::all();

        $canApproveStories = $this->canApproveStories();

        return view('admin.pages.story.index', compact(
            'stories',
            'categories',
            'totalStories',
            'publishedStories',
            'draftStories',
            'pendingStories',
            'rejectedStories',
            'featuredStories',
            'hiddenStories',
            'canApproveStories'
        ));
    }

    private function canApproveStories(): bool
    {
        $user = Auth::user();
        if ($user->role === User::ROLE_ADMIN_MAIN) {
            return true;
        }
        if ($user->role === User::ROLE_ADMIN_SUB) {
            return (int) Config::getConfig('admin_sub_can_approve_stories', 0) === 1;
        }
        return false;
    }

    public function approve(Request $request, Story $story)
    {
        if (!$this->canApproveStories()) {
            abort(403, 'Bạn không có quyền duyệt truyện.');
        }

        if ($story->status !== Story::STATUS_PENDING) {
            return redirect()->route('admin.stories.index')
                ->with('error', 'Chỉ có thể duyệt truyện đang chờ duyệt.');
        }

        $now = now();
        $story->update([
            'status' => Story::STATUS_PUBLISHED,
            'reviewed_at' => $now,
            'admin_note' => $request->admin_note,
        ]);

        StorySubmit::where('story_id', $story->id)->where('result', 'pending')->update([
            'result' => StorySubmit::RESULT_APPROVED,
            'admin_note' => $request->admin_note,
            'reviewed_at' => $now,
        ]);

        return redirect()->back()
            ->with('success', "Đã duyệt truyện '{$story->title}'.");
    }

    public function reject(Request $request, Story $story)
    {
        if (!$this->canApproveStories()) {
            abort(403, 'Bạn không có quyền từ chối truyện.');
        }

        if ($story->status !== Story::STATUS_PENDING) {
            return redirect()->route('admin.stories.index')
                ->with('error', 'Chỉ có thể từ chối truyện đang chờ duyệt.');
        }

        $request->validate(['admin_note' => 'required|string|max:1000'], [
            'admin_note.required' => 'Vui lòng nhập lý do từ chối.',
        ]);

        $now = now();
        $story->update([
            'status' => Story::STATUS_REJECTED,
            'admin_note' => $request->admin_note,
            'reviewed_at' => $now,
        ]);

        StorySubmit::where('story_id', $story->id)->where('result', 'pending')->update([
            'result' => StorySubmit::RESULT_REJECTED,
            'admin_note' => $request->admin_note,
            'reviewed_at' => $now,
        ]);

        return redirect()->back()
            ->with('success', "Đã từ chối truyện '{$story->title}'.");
    }

    public function edit(Story $story)
    {
        $categories = Category::all();
        $adminUsers = \App\Models\User::whereIn('role', ['admin_main', 'admin_sub'])->get();
        $chapters = $story->chapters()->orderBy('number', 'asc')->get();
        return view('admin.pages.story.edit', compact('story', 'categories', 'adminUsers', 'chapters'));
    }

    public function update(Request $request, Story $story)
    {
        $request->validate([
            'title' => 'required|max:255',
            'slug' => 'nullable|string|max:255|unique:stories,slug,' . $story->id,
            'description' => 'required',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,pending,published,rejected',
            'combo_price' => 'required_if:has_combo,on|nullable|integer|min:0',
            'author_name' => 'nullable|string|max:100',
            'featured_order' => 'nullable|integer|min:1',
            'editor_id' => 'nullable|exists:users,id',
            'story_type' => 'required|in:normal,zhihu',
            'search_keywords' => 'nullable|string|max:2000',
        ], [
            'title.required' => 'Tiêu đề không được để trống.',
            'title.max' => 'Tiêu đề không được quá 255 ký tự.',
            'slug.unique' => 'Slug đã tồn tại.',
            'slug.max' => 'Slug không được quá 255 ký tự.',
            'description.required' => 'Mô tả không được để trống.',
            'categories.required' => 'Chuyên mục không được để trống.',
            'categories.*.exists' => 'Chuyên mục không hợp lệ.',
            'cover.image' => 'Ảnh bìa phải là ảnh.',
            'cover.mimes' => 'Ảnh bìa phải có định dạng jpeg, png, jpg hoặc gif.',
            'cover.max' => 'Ảnh bìa không được quá 2MB.',
            'status.required' => 'Trạng thái không được để trống.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'combo_price.required_if' => 'Vui lòng nhập giá combo.',
            'combo_price.integer' => 'Giá combo phải là số nguyên.',
            'combo_price.min' => 'Giá combo không được âm.',
            'author_name.max' => 'Tên tác giả không được quá 100 ký tự.',
            'featured_order.integer' => 'Thứ tự đề cử phải là số nguyên.',
            'featured_order.min' => 'Thứ tự đề cử phải lớn hơn 0.',
            'editor_id.exists' => 'Biên tập viên không hợp lệ.',
            'story_type.required' => 'Loại truyện không được để trống.',
            'story_type.in' => 'Loại truyện không hợp lệ.',
            'search_keywords.max' => 'Từ khóa tìm kiếm không được quá 2000 ký tự.',
            'search_keywords.string' => 'Từ khóa tìm kiếm phải là chuỗi.',
        ]);

        // Validate editor_id if provided
        if ($request->editor_id) {
            $editor = User::find($request->editor_id);
            if (!$editor || !in_array($editor->role, ['admin_main', 'admin_sub'])) {
                return redirect()->route('admin.stories.edit', $story)
                    ->with('error', 'Editor được chọn phải có quyền admin_main hoặc admin_sub.')
                    ->withInput();
            }
        }

        DB::beginTransaction();
        try {
            $hasCombo = $request->has('has_combo');
            $comboPrice = $hasCombo ? $request->combo_price : 0;

            $isFeatured = $request->has('is_featured');
            $featuredOrder = $story->featured_order;

            if ($isFeatured) {
                if ($request->featured_order && $request->featured_order != $story->featured_order) {
                    $existingStory = Story::where('featured_order', $request->featured_order)
                        ->where('is_featured', true)
                        ->where('id', '!=', $story->id)
                        ->first();
                    if ($existingStory) {
                        throw new \Exception('Thứ tự đề cử ' . $request->featured_order . ' đã được sử dụng bởi truyện khác.');
                    }
                    $featuredOrder = $request->featured_order;
                } elseif (!$story->is_featured) {
                    $featuredOrder = $request->featured_order ?: Story::getNextFeaturedOrder();
                }
            } else {
                $featuredOrder = null;
            }

            // Generate slug
            $slug = $request->slug ?: Str::slug($request->title);
            $originalSlug = $slug;
            $counter = 1;
            
            // Ensure slug is unique (excluding current story)
            while (Story::where('slug', $slug)->where('id', '!=', $story->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $editorId = $request->editor_id;

            $data = [
                'title' => $request->title,
                'slug' => $slug,
                'description' => $request->description,
                'status' => $request->status,
                'completed' => $request->has('completed'),
                'has_combo' => $hasCombo,
                'combo_price' => $comboPrice,
                'author_name' => $request->author_name,
                'is_18_plus' => $request->has('is_18_plus'),
                'is_featured' => $isFeatured,
                'featured_order' => $featuredOrder,
                'editor_id' => $editorId,
                'story_type' => $request->story_type,
                'hide' => $request->boolean('hide'),
            ];

            if ($request->hasFile('cover')) {
                $oldImages = [
                    $story->cover,
                    $story->cover_thumbnail
                ];

                $coverPaths = $this->processAndSaveImage($request->file('cover'));

                $data['cover'] = $coverPaths['original'];
                $data['cover_thumbnail'] = $coverPaths['thumbnail'];
            }

            $story->update($data);
            $story->categories()->sync($request->categories);

            $story->keywords()->delete();
            $keywordsInput = trim((string) ($request->search_keywords ?? ''));
            if ($keywordsInput !== '') {
                $keywords = array_filter(array_map('trim', preg_split('/[\s,;]+/u', $keywordsInput)));
                foreach (array_unique($keywords) as $kw) {
                    if ($kw !== '') {
                        $story->keywords()->create(['keyword' => $kw]);
                    }
                }
            }

            DB::commit();
            if (isset($oldImages)) {
                Storage::disk('public')->delete($oldImages);
            }
            return redirect()->route('admin.stories.index')
                ->with('success', 'Truyện đã được cập nhật thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($coverPaths)) {
                Storage::disk('public')->delete([
                    $coverPaths['original'],
                    $coverPaths['thumbnail']
                ]);
            }
            Log::error('Error updating story:', ['error' => $e->getMessage()]);
            return redirect()->route('admin.stories.edit', $story)
                ->with('error', 'Có lỗi xảy ra khi cập nhật truyện: ' . $e->getMessage())->withInput();
        }
    }

    public function toggleFeatured(Story $story)
    {
        DB::beginTransaction();
        try {
            if ($story->is_featured) {
                $story->update([
                    'is_featured' => false,
                    'featured_order' => null
                ]);
                $message = "Đã bỏ đề cử truyện '{$story->title}'.";
            } else {
                $story->update([
                    'is_featured' => true,
                    'featured_order' => Story::getNextFeaturedOrder()
                ]);
                $message = "Đã đặt truyện '{$story->title}' làm truyện đề cử.";
            }

            DB::commit();

            return redirect()->route('admin.stories.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('admin.stories.index')
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update featured status
     */
    public function bulkUpdateFeatured(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'story_ids' => 'required|array',
            'story_ids.*' => 'exists:stories,id',
            'action' => 'required|in:feature,unfeature',
        ]);

        DB::beginTransaction();
        try {
            if ($request->action === 'feature') {
                $nextOrder = Story::getNextFeaturedOrder();

                foreach ($request->story_ids as $storyId) {
                    Story::where('id', $storyId)->update([
                        'is_featured' => true,
                        'featured_order' => $nextOrder++
                    ]);
                }

                $message = 'Đã đặt ' . count($request->story_ids) . ' truyện làm truyện đề cử.';
            } else {
                Story::whereIn('id', $request->story_ids)->update([
                    'is_featured' => false,
                    'featured_order' => null
                ]);

                $message = 'Đã bỏ đề cử ' . count($request->story_ids) . ' truyện.';
            }

            DB::commit();

            return redirect()->route('admin.stories.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('admin.stories.index')
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function bulkHideUnhide(Request $request)
    {
        $request->validate([
            'story_ids' => 'required|array',
            'story_ids.*' => 'exists:stories,id',
            'action' => 'required|in:hide,unhide',
        ]);

        $count = Story::whereIn('id', $request->story_ids)
            ->update(['hide' => $request->action === 'hide']);

        $message = $request->action === 'hide'
            ? "Đã ẩn {$count} truyện (không hiện ở trang chủ, tìm kiếm, thể loại...)."
            : "Đã bỏ ẩn {$count} truyện.";

        return redirect()->route('admin.stories.index')->with('success', $message);
    }

    public function show(Story $story)
    {
        $story->load(['user', 'categories', 'storySubmits']);
        $story->loadCount('chapters');

        $story_purchases = $story->purchases()
            ->with('user')
            ->latest()
            ->paginate(10, ['*'], 'story_page');
        $story_purchases_count = $story->purchases()->count();

        $chapter_purchases = \App\Models\ChapterPurchase::whereHas('chapter', function ($query) use ($story) {
            $query->where('story_id', $story->id);
        })->with(['user', 'chapter'])
            ->latest()
            ->paginate(10, ['*'], 'chapter_page');
        $chapter_purchases_count = \App\Models\ChapterPurchase::whereHas('chapter', function ($query) use ($story) {
            $query->where('story_id', $story->id);
        })->count();

        $bookmarks = $story->bookmarks()
            ->with(['user', 'lastChapter'])
            ->latest()
            ->paginate(10, ['*'], 'bookmark_page');
        $bookmarks_count = $story->bookmarks()->count();

        $story_revenue = $story->purchases()->sum('amount_paid');
        $chapter_revenue = \App\Models\ChapterPurchase::whereHas('chapter', function ($query) use ($story) {
            $query->where('story_id', $story->id);
        })->sum('amount_paid');
        $total_revenue = $story_revenue + $chapter_revenue;

        $canApproveStories = $this->canApproveStories();

        return view('admin.pages.story.show', compact(
            'story',
            'story_purchases',
            'story_purchases_count',
            'chapter_purchases',
            'chapter_purchases_count',
            'bookmarks',
            'bookmarks_count',
            'total_revenue',
            'canApproveStories'
        ));
    }

    public function destroy(Story $story)
    {
        DB::beginTransaction();

        try {
            $story->banners()->delete();

            $story->categories()->detach();

            $story->delete();

            DB::commit();

            Storage::disk('public')->delete([
                $story->cover,
                $story->cover_thumbnail
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting story:', ['error' => $e->getMessage()]);
            return redirect()->route('admin.stories.index')
                ->with('error', 'Có lỗi xảy ra khi xóa truyện.');
        }

        return redirect()->route('admin.stories.index')
            ->with('success', 'Truyện đã được xóa thành công.');
    }

    /**
     * Update views of a chapter
     */
    public function updateChapterViews(Request $request, Story $story, Chapter $chapter)
    {
        $request->validate([
            'views' => 'required|integer|min:0',
        ], [
            'views.required' => 'Lượt xem không được để trống.',
            'views.integer' => 'Lượt xem phải là số nguyên.',
            'views.min' => 'Lượt xem không được âm.',
        ]);

        try {
            $chapter->update([
                'views' => $request->views
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Đã cập nhật lượt xem thành công',
                    'views' => $chapter->views,
                    'total_views' => $story->fresh()->total_views
                ]);
            }

            return redirect()->back()
                ->with('success', 'Đã cập nhật lượt xem chương ' . $chapter->number . ' thành công');
        } catch (\Exception $e) {
            Log::error('Error updating chapter views:', ['error' => $e->getMessage()]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update views of all chapters - Optimized with raw SQL
     */
    public function bulkUpdateChapterViews(Request $request, Story $story)
    {
        $request->validate([
            'action' => 'required|in:set,set_total,add,subtract,multiply,divide',
            'value' => 'required|numeric|min:0',
        ], [
            'action.required' => 'Vui lòng chọn hành động.',
            'action.in' => 'Hành động không hợp lệ.',
            'value.required' => 'Vui lòng nhập giá trị.',
            'value.numeric' => 'Giá trị phải là số.',
            'value.min' => 'Giá trị không được âm.',
        ]);

        try {
            $action = $request->action;
            $value = (float) $request->value;
            $storyId = $story->id;

            DB::beginTransaction();

            // Optimized: Use raw SQL for bulk update instead of looping
            if ($action === 'set_total') {
                // Set total views: distribute evenly or proportionally
                $totalChapters = $story->chapters()->count();
                if ($totalChapters > 0) {
                    $viewsPerChapter = (int) ($value / $totalChapters);
                    $remainder = (int) ($value % $totalChapters);
                    
                    // Update all chapters with base views
                    DB::table('chapters')
                        ->where('story_id', $storyId)
                        ->update(['views' => $viewsPerChapter]);
                    
                    // Distribute remainder to first N chapters
                    if ($remainder > 0) {
                        DB::table('chapters')
                            ->where('story_id', $storyId)
                            ->orderBy('number', 'asc')
                            ->limit($remainder)
                            ->update([
                                'views' => DB::raw('views + 1')
                            ]);
                    }
                }
            } else {
                // Use raw SQL for other actions - much faster than looping
                $sql = match($action) {
                    'set' => "UPDATE chapters SET views = ? WHERE story_id = ?",
                    'add' => "UPDATE chapters SET views = GREATEST(0, views + ?) WHERE story_id = ?",
                    'subtract' => "UPDATE chapters SET views = GREATEST(0, views - ?) WHERE story_id = ?",
                    'multiply' => "UPDATE chapters SET views = GREATEST(0, FLOOR(views * ?)) WHERE story_id = ?",
                    'divide' => "UPDATE chapters SET views = GREATEST(0, FLOOR(views / ?)) WHERE story_id = ? AND ? > 0",
                    default => null
                };

                if ($sql) {
                    if ($action === 'divide') {
                        DB::statement($sql, [$value, $storyId, $value]);
                    } else {
                        DB::statement($sql, [$value, $storyId]);
                    }
                }
            }

            DB::commit();

            // Refresh story to get updated total views
            $story->refresh();
            $chapters = $story->chapters()->select('id', 'views')->get();

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Đã cập nhật lượt xem cho tất cả chương thành công',
                    'total_views' => $story->total_views,
                    'chapters' => $chapters->map(function($chapter) {
                        return [
                            'id' => $chapter->id,
                            'views' => $chapter->views
                        ];
                    })
                ]);
            }

            return redirect()->back()
                ->with('success', 'Đã cập nhật lượt xem cho tất cả chương thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk updating chapter views:', ['error' => $e->getMessage()]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
