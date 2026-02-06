<?php

namespace App\Http\Controllers\Author;

use Carbon\Carbon;
use App\Models\Story;
use App\Models\Category;
use App\Models\Config;
use App\Models\StorySubmit;
use App\Models\StoryEditRequest;
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
        $query = Story::with(['categories'])
            ->withCount('chapters')
            ->where('user_id', Auth::id());

        $totalStories = Story::where('user_id', Auth::id())->count();
        $publishedStories = Story::where('user_id', Auth::id())->where('status', 'published')->count();
        $draftStories = Story::where('user_id', Auth::id())->where('status', 'draft')->count();
        $pendingStories = Story::where('user_id', Auth::id())->where('status', 'pending')->count();
        $rejectedStories = Story::where('user_id', Auth::id())->where('status', 'rejected')->count();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->category) {
            $query->whereHas('categories', fn($q) => $q->where('categories.id', $request->category));
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(fn($q) => $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('author_name', 'like', "%{$search}%"));
        }

        $query->orderBy('created_at', 'desc');
        $stories = $query->paginate(15);
        $stories->appends($request->only(['status', 'category', 'search']));

        $categories = Category::all();

        $canPublishZhihu = Auth::user()->can_publish_zhihu ?? false;
        return view('pages.author.story.index', compact(
            'stories',
            'categories',
            'totalStories',
            'publishedStories',
            'draftStories',
            'pendingStories',
            'rejectedStories',
            'canPublishZhihu'
        ));
    }

    public function create()
    {
        $maxIncomplete = (int) Config::getConfig('author_max_incomplete_stories', 0);
        if ($maxIncomplete > 0) {
            $incompleteCount = Story::where('user_id', Auth::id())
                ->where('completed', false)
                ->whereIn('status', ['draft', 'pending', 'rejected', 'published'])
                ->count();
            if ($incompleteCount >= $maxIncomplete) {
                return redirect()->route('author.stories.index')
                    ->with('error', "Bạn đã đạt giới hạn {$maxIncomplete} truyện chưa hoàn thành. Vui lòng hoàn thành hoặc xóa bớt truyện để đăng mới.");
            }
        }
        $categories = Category::all();
        $canPublishZhihu = Auth::user()->can_publish_zhihu ?? false;
        return view('pages.author.story.create', compact('categories', 'canPublishZhihu'));
    }

    public function store(Request $request)
    {
        $maxIncomplete = (int) Config::getConfig('author_max_incomplete_stories', 0);
        if ($maxIncomplete > 0) {
            $incompleteCount = Story::where('user_id', Auth::id())
                ->where('completed', false)
                ->whereIn('status', ['draft', 'pending', 'rejected', 'published'])
                ->count();
            if ($incompleteCount >= $maxIncomplete) {
                return redirect()->route('author.stories.index')
                    ->with('error', "Bạn đã đạt giới hạn {$maxIncomplete} truyện chưa hoàn thành.");
            }
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'cover' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,pending',
            'story_type' => 'nullable|in:normal,zhihu',
            'combo_price' => 'required_if:has_combo,on|nullable|integer|min:0',
            'author_name' => 'required|string|max:100',
            'submitted_note' => 'nullable|string|max:1000',
        ], [
            'title.required' => 'Tiêu đề truyện không được để trống.',
            'title.max' => 'Tiêu đề truyện không được vượt quá 255 ký tự.',
            'description.required' => 'Mô tả truyện không được để trống.',
            'categories.required' => 'Vui lòng chọn ít nhất một thể loại.',
            'categories.*.exists' => 'Thể loại không hợp lệ.',
            'cover.required' => 'Vui lòng chọn ảnh bìa.',
            'cover.image' => 'Ảnh bìa phải là file ảnh (jpeg, png, jpg, gif).',
            'cover.mimes' => 'Ảnh bìa phải có định dạng jpeg, png, jpg hoặc gif.',
            'cover.max' => 'Ảnh bìa không được vượt quá 2MB.',
            'status.required' => 'Vui lòng chọn trạng thái (Bản nháp hoặc Gửi duyệt).',
            'status.in' => 'Trạng thái phải là Bản nháp hoặc Gửi duyệt.',
            'story_type.in' => 'Loại truyện không hợp lệ.',
            'combo_price.required_if' => 'Khi bật bán combo, vui lòng nhập giá combo.',
            'combo_price.integer' => 'Giá combo phải là số nguyên.',
            'combo_price.min' => 'Giá combo không được nhỏ hơn 0.',
            'author_name.required' => 'Tên tác giả hiển thị không được để trống.',
            'author_name.max' => 'Tên tác giả không được vượt quá 100 ký tự.',
            'submitted_note.max' => 'Ghi chú gửi duyệt không được vượt quá 1000 ký tự.',
        ], [
            'title' => 'Tiêu đề truyện',
            'description' => 'Mô tả',
            'categories' => 'Thể loại',
            'cover' => 'Ảnh bìa',
            'status' => 'Trạng thái',
            'story_type' => 'Loại truyện',
            'combo_price' => 'Giá combo',
            'author_name' => 'Tên tác giả hiển thị',
            'submitted_note' => 'Ghi chú gửi duyệt',
        ]);

        DB::beginTransaction();
        try {
            $coverPaths = $this->processAndSaveImage($request->file('cover'));

            $hasCombo = $request->has('has_combo');
            $comboPrice = $hasCombo ? (int) $request->combo_price : 0;

            $slug = Str::slug($request->title);
            if (Story::where('slug', $slug)->exists()) {
                return redirect()->route('author.stories.create')
                    ->with('error', 'Tiêu đề truyện trùng với truyện khác. Vui lòng đổi tiêu đề.')
                    ->withInput();
            }

            $storyType = ($request->story_type === 'zhihu' && Auth::user()->can_publish_zhihu) ? 'zhihu' : 'normal';

            $data = [
                'user_id' => Auth::id(),
                'title' => $request->title,
                'slug' => $slug,
                'description' => $request->description,
                'status' => $request->status,
                'story_type' => $storyType,
                'cover' => $coverPaths['original'],
                'cover_thumbnail' => $coverPaths['thumbnail'],
                'has_combo' => $hasCombo,
                'combo_price' => $comboPrice,
                'author_name' => $request->author_name ?: Auth::user()->name,
                'is_18_plus' => $request->has('is_18_plus'),
                'editor_id' => Auth::user()->id,
                'completed' => false,
            ];

            if ($request->status === 'pending') {
                $data['submitted_note'] = $request->submitted_note;
                $data['submitted_at'] = now();
            }

            $story = Story::create($data);
            $story->categories()->attach($request->categories);

            if ($request->status === 'pending') {
                StorySubmit::create([
                    'story_id' => $story->id,
                    'submitted_note' => $request->submitted_note,
                    'submitted_at' => now(),
                    'result' => StorySubmit::RESULT_PENDING,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($coverPaths)) {
                Storage::disk('public')->delete([$coverPaths['original'], $coverPaths['thumbnail']]);
            }
            Log::error('Error creating story:', ['error' => $e->getMessage()]);
            return redirect()->route('author.stories.create')
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }

        $msg = $request->status === 'pending' ? 'Truyện đã được gửi duyệt. Admin sẽ xem xét và phản hồi.' : 'Truyện đã được lưu bản nháp.';
        return redirect()->route('author.stories.index')->with('success', $msg);
    }

    public function show(Story $story)
    {
        $this->authorizeOwnership($story);

        $story->load(['categories', 'storySubmits'])->loadCount('chapters');

        $story_purchases = $story->purchases()->with('user')->latest()->paginate(10, ['*'], 'story_page');
        $story_purchases_count = $story->purchases()->count();

        $chapter_purchases = \App\Models\ChapterPurchase::whereHas('chapter', fn($q) => $q->where('story_id', $story->id))
            ->with(['user', 'chapter'])
            ->latest()
            ->paginate(10, ['*'], 'chapter_page');
        $chapter_purchases_count = \App\Models\ChapterPurchase::whereHas('chapter', fn($q) => $q->where('story_id', $story->id))->count();

        $bookmarks = $story->bookmarks()->with(['user', 'lastChapter'])->latest()->paginate(10, ['*'], 'bookmark_page');
        $bookmarks_count = $story->bookmarks()->count();

        $story_revenue = $story->purchases()->sum('amount_paid');
        $chapter_revenue = \App\Models\ChapterPurchase::whereHas('chapter', fn($q) => $q->where('story_id', $story->id))->sum('amount_paid');
        $total_revenue = $story_revenue + $chapter_revenue;

        return view('pages.author.story.show', compact(
            'story',
            'story_purchases',
            'story_purchases_count',
            'chapter_purchases',
            'chapter_purchases_count',
            'bookmarks',
            'bookmarks_count',
            'total_revenue'
        ));
    }

    public function edit(Story $story)
    {
        $this->authorizeOwnership($story);

        $categories = Category::all();
        $chapters = $story->chapters()->orderBy('number', 'asc')->get();
        $pendingEditRequest = $story->editRequests()->where('status', StoryEditRequest::STATUS_PENDING)->first();
        $hasPendingEditRequest = (bool) $pendingEditRequest;

        $canPublishZhihu = Auth::user()->can_publish_zhihu ?? false;
        return view('pages.author.story.edit', compact('story', 'categories', 'chapters', 'hasPendingEditRequest', 'pendingEditRequest', 'canPublishZhihu'));
    }

    public function update(Request $request, Story $story)
    {
        $this->authorizeOwnership($story);

        if ($story->status === 'published') {
            return $this->updatePublishedStory($request, $story);
        }

        if (!in_array($story->status, ['draft', 'pending', 'rejected'])) {
            return redirect()->route('author.stories.index')
                ->with('error', 'Trạng thái truyện không cho phép chỉnh sửa.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,pending',
            'story_type' => 'nullable|in:normal,zhihu',
            'combo_price' => 'required_if:has_combo,on|nullable|integer|min:0',
            'author_name' => 'required|string|max:100',
            'submitted_note' => 'nullable|string|max:1000',
        ], [
            'title.required' => 'Tiêu đề truyện không được để trống.',
            'title.max' => 'Tiêu đề truyện không được vượt quá 255 ký tự.',
            'description.required' => 'Mô tả truyện không được để trống.',
            'categories.required' => 'Vui lòng chọn ít nhất một thể loại.',
            'categories.*.exists' => 'Thể loại không hợp lệ.',
            'cover.image' => 'Ảnh bìa phải là file ảnh (jpeg, png, jpg, gif).',
            'cover.mimes' => 'Ảnh bìa phải có định dạng jpeg, png, jpg hoặc gif.',
            'cover.max' => 'Ảnh bìa không được vượt quá 2MB.',
            'status.required' => 'Vui lòng chọn trạng thái (Bản nháp hoặc Gửi duyệt).',
            'status.in' => 'Trạng thái phải là Bản nháp hoặc Gửi duyệt.',
            'story_type.in' => 'Loại truyện không hợp lệ.',
            'combo_price.required_if' => 'Khi bật bán combo, vui lòng nhập giá combo.',
            'combo_price.integer' => 'Giá combo phải là số nguyên.',
            'combo_price.min' => 'Giá combo không được nhỏ hơn 0.',
            'author_name.required' => 'Tên tác giả hiển thị không được để trống.',
            'author_name.max' => 'Tên tác giả không được vượt quá 100 ký tự.',
            'submitted_note.max' => 'Ghi chú gửi duyệt không được vượt quá 1000 ký tự.',
        ], [
            'title' => 'Tiêu đề truyện',
            'description' => 'Mô tả',
            'categories' => 'Thể loại',
            'cover' => 'Ảnh bìa',
            'status' => 'Trạng thái',
            'story_type' => 'Loại truyện',
            'combo_price' => 'Giá combo',
            'author_name' => 'Tên tác giả hiển thị',
            'submitted_note' => 'Ghi chú gửi duyệt',
        ]);

        DB::beginTransaction();
        try {
            $hasCombo = $request->has('has_combo');
            $comboPrice = $hasCombo ? (int) $request->combo_price : 0;
            $storyType = ($request->story_type === 'zhihu' && Auth::user()->can_publish_zhihu) ? 'zhihu' : 'normal';

            $slug = Str::slug($request->title);
            if (Story::where('slug', $slug)->where('id', '!=', $story->id)->exists()) {
                return redirect()->route('author.stories.edit', $story)
                    ->with('error', 'Tiêu đề truyện trùng với truyện khác. Vui lòng đổi tiêu đề.')
                    ->withInput();
            }

            $data = [
                'title' => $request->title,
                'slug' => $slug,
                'description' => $request->description,
                'status' => $request->status,
                'story_type' => $storyType,
                'has_combo' => $hasCombo,
                'combo_price' => $comboPrice,
                'author_name' => $request->author_name ?: Auth::user()->name,
                'is_18_plus' => $request->has('is_18_plus'),
            ];

            if ($request->status === 'pending') {
                $data['submitted_note'] = $request->submitted_note;
                $data['submitted_at'] = now();
                $data['admin_note'] = null;
                $data['reviewed_at'] = null;
            } else {
                $data['submitted_note'] = null;
                $data['submitted_at'] = null;
            }

            if ($request->hasFile('cover')) {
                $oldImages = [$story->cover, $story->cover_thumbnail];
                $coverPaths = $this->processAndSaveImage($request->file('cover'));
                $data['cover'] = $coverPaths['original'];
                $data['cover_thumbnail'] = $coverPaths['thumbnail'];
            }

            $story->update($data);
            $story->categories()->sync($request->categories);

            if ($request->status === 'pending') {
                StorySubmit::create([
                    'story_id' => $story->id,
                    'submitted_note' => $request->submitted_note,
                    'submitted_at' => now(),
                    'result' => StorySubmit::RESULT_PENDING,
                ]);
            }

            DB::commit();
            if (isset($oldImages)) {
                Storage::disk('public')->delete($oldImages);
            }

            $msg = $request->status === 'pending' ? 'Truyện đã được gửi duyệt.' : 'Truyện đã được cập nhật.';
            return redirect()->route('author.stories.index')->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($coverPaths)) {
                Storage::disk('public')->delete([$coverPaths['original'] ?? null, $coverPaths['thumbnail'] ?? null]);
            }
            Log::error('Error updating story:', ['error' => $e->getMessage()]);
            return redirect()->route('author.stories.edit', $story)
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    private function updatePublishedStory(Request $request, Story $story)
    {
        $newCompleted = $request->has('completed');
        $hasCombo = $request->has('has_combo');
        $comboPrice = $hasCombo ? (int) ($request->combo_price ?? 0) : 0;

        $descNorm = fn ($s) => trim(preg_replace('/\s+/', ' ', strip_tags($s ?? '')));
        $onlyQuickFieldsChanged = (
            trim($request->title ?? '') === trim($story->title ?? '') &&
            Str::slug($request->title) === $story->slug &&
            $descNorm($request->description) === $descNorm($story->description) &&
            trim($request->author_name ?: Auth::user()->name) === trim($story->author_name ?: Auth::user()->name) &&
            (bool) $request->has('is_18_plus') === (bool) $story->is_18_plus &&
            !$request->hasFile('cover')
        );

        $currentCategoryIds = $story->categories->pluck('id')->sort()->values()->all();
        $requestCategoryIds = collect($request->categories ?? [])->filter()->map(fn ($v) => (int) $v)->sort()->values()->all();
        if ($currentCategoryIds !== $requestCategoryIds) {
            $onlyQuickFieldsChanged = false;
        }

        if ($onlyQuickFieldsChanged) {
            $request->validate([
                'combo_price' => 'required_if:has_combo,on|nullable|integer|min:0',
            ], [
                'combo_price.required_if' => 'Khi bật bán combo, vui lòng nhập giá combo.',
                'combo_price.integer' => 'Giá combo phải là số nguyên.',
                'combo_price.min' => 'Giá combo không được nhỏ hơn 0.',
            ], ['combo_price' => 'Giá combo']);
            $story->update([
                'completed' => $newCompleted,
                'has_combo' => $hasCombo,
                'combo_price' => $comboPrice,
            ]);
            return redirect()->route('author.stories.index')->with('success', 'Đã cập nhật. Trạng thái hoàn thành và bán combo áp dụng ngay, không cần duyệt.');
        }

        // Có thay đổi nội dung khác → cần gửi yêu cầu duyệt. Chặn nếu đã có yêu cầu đang chờ.
        if ($story->editRequests()->where('status', StoryEditRequest::STATUS_PENDING)->exists()) {
            return redirect()->route('author.stories.edit', $story)
                ->with('error', 'Bạn đã có yêu cầu chỉnh sửa đang chờ duyệt. Vui lòng đợi admin xử lý hoặc rút lại yêu cầu trước khi gửi mới.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'combo_price' => 'required_if:has_combo,on|nullable|integer|min:0',
            'author_name' => 'nullable|string|max:100',
            'review_note' => 'nullable|string|max:1000',
        ], [
            'title.required' => 'Tiêu đề truyện không được để trống.',
            'title.max' => 'Tiêu đề truyện không được vượt quá 255 ký tự.',
            'description.required' => 'Mô tả truyện không được để trống.',
            'categories.required' => 'Vui lòng chọn ít nhất một thể loại.',
            'categories.*.exists' => 'Thể loại không hợp lệ.',
            'cover.image' => 'Ảnh bìa phải là file ảnh (jpeg, png, jpg, gif).',
            'cover.mimes' => 'Ảnh bìa phải có định dạng jpeg, png, jpg hoặc gif.',
            'cover.max' => 'Ảnh bìa không được vượt quá 2MB.',
            'combo_price.required_if' => 'Khi bật bán combo, vui lòng nhập giá combo.',
            'combo_price.integer' => 'Giá combo phải là số nguyên.',
            'combo_price.min' => 'Giá combo không được nhỏ hơn 0.',
            'author_name.max' => 'Tên tác giả không được vượt quá 100 ký tự.',
            'review_note.max' => 'Ghi chú yêu cầu chỉnh sửa không được vượt quá 1000 ký tự.',
        ], [
            'title' => 'Tiêu đề truyện',
            'description' => 'Mô tả',
            'categories' => 'Thể loại',
            'cover' => 'Ảnh bìa',
            'combo_price' => 'Giá combo',
            'author_name' => 'Tên tác giả hiển thị',
            'review_note' => 'Ghi chú yêu cầu chỉnh sửa',
        ]);

        $slug = Str::slug($request->title);
        if (Story::where('slug', $slug)->where('id', '!=', $story->id)->exists()) {
            return redirect()->route('author.stories.edit', $story)
                ->with('error', 'Tiêu đề truyện trùng với truyện khác. Vui lòng đổi tiêu đề.')
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $hasCombo = $request->has('has_combo');
            $comboPrice = $hasCombo ? (int) $request->combo_price : 0;

            $editData = [
                'story_id' => $story->id,
                'user_id' => Auth::id(),
                'title' => $request->title,
                'slug' => $slug,
                'description' => $request->description,
                'author_name' => $request->author_name ?: Auth::user()->name,
                'is_18_plus' => $request->has('is_18_plus'),
                'has_combo' => $hasCombo,
                'combo_price' => $comboPrice,
                'categories_data' => json_encode($request->categories),
                'review_note' => $request->review_note,
                'status' => StoryEditRequest::STATUS_PENDING,
                'submitted_at' => now(),
            ];

            if ($request->hasFile('cover')) {
                $coverPaths = $this->processAndSaveImage($request->file('cover'));
                $editData['cover'] = $coverPaths['original'];
                $editData['cover_thumbnail'] = $coverPaths['thumbnail'];
            } else {
                $editData['cover'] = $story->cover;
                $editData['cover_thumbnail'] = $story->cover_thumbnail;
            }

            StoryEditRequest::create($editData);
            DB::commit();

            return redirect()->route('author.stories.index')
                ->with('success', 'Yêu cầu chỉnh sửa đã được gửi. Admin sẽ xem xét và áp dụng thay đổi.');
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($coverPaths)) {
                Storage::disk('public')->delete([$coverPaths['original'] ?? null, $coverPaths['thumbnail'] ?? null]);
            }
            Log::error('Error creating story edit request:', ['error' => $e->getMessage()]);
            return redirect()->route('author.stories.edit', $story)
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    public function submitForReview(Request $request, Story $story)
    {
        $this->authorizeOwnership($story);

        if (!in_array($story->status, ['draft', 'rejected'])) {
            return redirect()->route('author.stories.index')
                ->with('error', 'Chỉ truyện Nháp hoặc Từ chối mới có thể gửi duyệt.');
        }

        $story->update([
            'status' => Story::STATUS_PENDING,
            'submitted_note' => $request->submitted_note,
            'submitted_at' => now(),
            'admin_note' => null,
            'reviewed_at' => null,
        ]);

        StorySubmit::create([
            'story_id' => $story->id,
            'submitted_note' => $request->submitted_note,
            'submitted_at' => now(),
            'result' => StorySubmit::RESULT_PENDING,
        ]);

        return redirect()->back()->with('success', 'Đã gửi duyệt. Admin sẽ xem xét và phản hồi.');
    }

    public function withdrawEditRequest(Story $story, StoryEditRequest $editRequest)
    {
        $this->authorizeOwnership($story);

        if ($editRequest->story_id !== $story->id || $editRequest->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền rút yêu cầu này.');
        }
        if ($editRequest->status !== StoryEditRequest::STATUS_PENDING) {
            return redirect()->back()->with('error', 'Chỉ có thể rút yêu cầu đang chờ duyệt.');
        }

        $editRequest->delete();
        return redirect()->back()->with('success', 'Đã rút lại yêu cầu chỉnh sửa.');
    }

    public function destroy(Story $story)
    {
        $this->authorizeOwnership($story);

        if (!in_array($story->status, ['draft', 'rejected'])) {
            return redirect()->route('author.stories.index')
                ->with('error', 'Chỉ có thể xóa truyện ở trạng thái Bản nháp hoặc Từ chối.');
        }

        try {
            $story->categories()->detach();
            $story->delete();
            Storage::disk('public')->delete([$story->cover, $story->cover_thumbnail]);
        } catch (\Exception $e) {
            Log::error('Error deleting story:', ['error' => $e->getMessage()]);
            return redirect()->route('author.stories.index')->with('error', 'Có lỗi xảy ra khi xóa truyện.');
        }

        return redirect()->route('author.stories.index')->with('success', 'Truyện đã được xóa.');
    }

    private function authorizeOwnership(Story $story): void
    {
        if ($story->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền thao tác truyện này.');
        }
    }
}
