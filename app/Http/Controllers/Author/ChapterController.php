<?php

namespace App\Http\Controllers\Author;

use App\Models\Story;
use App\Models\Chapter;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ChapterController extends Controller
{
    private function authorizeStory(Story $story): void
    {
        if ($story->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền thao tác truyện này.');
        }
    }

    public function index(Request $request, Story $story)
    {
        $this->authorizeStory($story);

        $query = $story->chapters();
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $search = $request->search;
            $query->where(fn($q) => $q->where('title', 'like', "%$search%")->orWhere('number', 'like', "%$search%"));
        }

        $chapters = $query->orderBy('number', 'DESC')->paginate(15);
        $chapters->appends($request->only(['status', 'search']));

        foreach ($chapters as $chapter) {
            $chapter->content_preview = mb_substr(strip_tags($chapter->content), 0, 97, 'UTF-8') . '...';
        }

        $totalChapters = $story->chapters()->count();
        $publishedChapters = $story->chapters()->where('status', 'published')->count();
        $draftChapters = $story->chapters()->where('status', 'draft')->count();

        return view('pages.author.chapter.index', compact('story', 'chapters', 'totalChapters', 'publishedChapters', 'draftChapters'));
    }

    public function create(Story $story)
    {
        $this->authorizeStory($story);
        $nextChapterNumber = ($story->chapters()->max('number') ?? 0) + 1;
        return view('pages.author.chapter.create', compact('story', 'nextChapterNumber'));
    }

    public function store(Request $request, Story $story)
    {
        $this->authorizeStory($story);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'number' => ['required', 'integer', 'min:1', function ($attr, $v, $fail) use ($story) {
                if ($story->chapters()->where('number', $v)->exists()) {
                    $fail('Số chương ' . $v . ' đã tồn tại trong truyện này.');
                }
            }],
            'status' => 'required|in:draft,published',
            'price' => 'nullable|integer|min:0',
            'scheduled_publish_at' => 'nullable|date|after:now',
        ], [
            'title.required' => 'Tiêu đề chương không được để trống.',
            'title.max' => 'Tiêu đề chương không được vượt quá 255 ký tự.',
            'content.required' => 'Nội dung chương không được để trống.',
            'number.required' => 'Số thứ tự chương không được để trống.',
            'number.integer' => 'Số thứ tự chương phải là số nguyên.',
            'number.min' => 'Số thứ tự chương phải lớn hơn 0.',
            'status.required' => 'Vui lòng chọn trạng thái (Bản nháp hoặc Xuất bản).',
            'status.in' => 'Trạng thái phải là Bản nháp hoặc Xuất bản.',
            'price.integer' => 'Giá chương phải là số nguyên.',
            'price.min' => 'Giá chương không được nhỏ hơn 0.',
            'scheduled_publish_at.date' => 'Thời gian hẹn đăng không đúng định dạng.',
            'scheduled_publish_at.after' => 'Thời gian hẹn đăng phải sau thời điểm hiện tại.',
        ], [
            'title' => 'Tiêu đề chương',
            'content' => 'Nội dung chương',
            'number' => 'Số thứ tự chương',
            'status' => 'Trạng thái',
            'price' => 'Giá chương',
            'scheduled_publish_at' => 'Thời gian hẹn đăng',
        ]);

        try {
            $isZhihu = ($story->story_type ?? 'normal') === 'zhihu';
            $isFree = $isZhihu || $request->has('is_free');
            $price = $isZhihu ? 0 : ($isFree ? 0 : $request->price);

            $publishedAt = null;
            $scheduledPublishAt = null;

            if ($request->status === 'draft' && $request->scheduled_publish_at) {
                $scheduledTime = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $request->scheduled_publish_at, 'Asia/Ho_Chi_Minh');
                if ($scheduledTime->gt(now())) {
                    $scheduledPublishAt = $scheduledTime;
                } else {
                    $publishedAt = $scheduledTime;
                }
            } elseif ($request->status === 'published') {
                $publishedAt = now();
            }

            $passwordEncrypted = null;
            $passwordHint = null;
            if (!$isZhihu && $isFree && $request->get('has_password') == '1') {
                $request->validate([
                    'chapter_password' => 'required|string|min:1|max:100',
                    'password_hint' => 'required|string|min:1|max:1000',
                ], [
                    'chapter_password.required' => 'Vui lòng nhập mật khẩu chương.',
                    'password_hint.required' => 'Gợi ý mật khẩu là bắt buộc khi đặt mật khẩu chương.',
                ]);
                $passwordEncrypted = Crypt::encryptString($request->chapter_password);
                $passwordHint = $request->password_hint;
            }

            $chapter = $story->chapters()->create([
                'title' => $request->title,
                'content' => $this->normalizeChapterContent($request->content),
                'number' => $request->number,
                'status' => $request->status,
                'is_free' => $isFree,
                'price' => $price,
                'slug' => 'temp-' . time(),
                'published_at' => $publishedAt,
                'scheduled_publish_at' => $scheduledPublishAt,
                'password_encrypted' => $passwordEncrypted,
                'password_hint' => $passwordHint,
                'user_id' => Auth::id(),
            ]);
            $chapter->update(['slug' => $chapter->id . '-chuong' . $request->number]);

            return redirect()->route('author.stories.chapters.index', $story)->with('success', 'Đã tạo chương ' . $request->number);
        } catch (\Exception $e) {
            Log::error('Chapter creation error:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Có lỗi xảy ra.')->withInput();
        }
    }

    public function show(Story $story, Chapter $chapter)
    {
        $this->authorizeStory($story);
        return view('pages.author.chapter.show', compact('story', 'chapter'));
    }

    public function edit(Story $story, Chapter $chapter)
    {
        $this->authorizeStory($story);
        $prevChapter = $story->chapters()->where('number', '<', $chapter->number)->orderByDesc('number')->first();
        $nextChapter = $story->chapters()->where('number', '>', $chapter->number)->orderBy('number')->first();
        return view('pages.author.chapter.edit', compact('story', 'chapter', 'prevChapter', 'nextChapter'));
    }

    public function update(Request $request, Story $story, Chapter $chapter)
    {
        $this->authorizeStory($story);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'number' => ['required', 'integer', 'min:1', function ($attr, $v, $fail) use ($story, $chapter) {
                if ($story->chapters()->where('number', $v)->where('id', '!=', $chapter->id)->exists()) {
                    $fail('Số chương ' . $v . ' đã tồn tại trong truyện này.');
                }
            }],
            'status' => 'required|in:draft,published',
            'price' => 'nullable|integer|min:0',
            'scheduled_publish_at' => 'nullable|date|after:now',
        ], [
            'title.required' => 'Tiêu đề chương không được để trống.',
            'title.max' => 'Tiêu đề chương không được vượt quá 255 ký tự.',
            'content.required' => 'Nội dung chương không được để trống.',
            'number.required' => 'Số thứ tự chương không được để trống.',
            'number.integer' => 'Số thứ tự chương phải là số nguyên.',
            'number.min' => 'Số thứ tự chương phải lớn hơn 0.',
            'status.required' => 'Vui lòng chọn trạng thái (Bản nháp hoặc Xuất bản).',
            'status.in' => 'Trạng thái phải là Bản nháp hoặc Xuất bản.',
            'price.integer' => 'Giá chương phải là số nguyên.',
            'price.min' => 'Giá chương không được nhỏ hơn 0.',
            'scheduled_publish_at.date' => 'Thời gian hẹn đăng không đúng định dạng.',
            'scheduled_publish_at.after' => 'Thời gian hẹn đăng phải sau thời điểm hiện tại.',
        ], [
            'title' => 'Tiêu đề chương',
            'content' => 'Nội dung chương',
            'number' => 'Số thứ tự chương',
            'status' => 'Trạng thái',
            'price' => 'Giá chương',
            'scheduled_publish_at' => 'Thời gian hẹn đăng',
        ]);

        try {
            $isZhihu = ($story->story_type ?? 'normal') === 'zhihu';
            $isFree = $isZhihu || $request->has('is_free');
            $price = $isZhihu ? 0 : ($isFree ? 0 : $request->price);

            $scheduledPublishAt = null;
            $publishedAt = $chapter->published_at;

            if ($request->status === 'published') {
                $publishedAt = now();
                $scheduledPublishAt = null;
            } elseif ($request->status === 'draft' && $request->filled('scheduled_publish_at')) {
                $scheduledTime = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $request->scheduled_publish_at, 'Asia/Ho_Chi_Minh');
                if ($scheduledTime->gt(now())) {
                    $scheduledPublishAt = $scheduledTime;
                    $publishedAt = null;
                } else {
                    $publishedAt = $scheduledTime;
                    $scheduledPublishAt = null;
                }
            } else {
                $scheduledPublishAt = null;
            }

            $passwordEncrypted = $chapter->password_encrypted;
            $passwordHint = $chapter->password_hint;
            if (!$isZhihu && $isFree && $request->get('has_password') == '1') {
                $request->validate([
                    'password_hint' => 'required|string|min:1|max:1000',
                ], [
                    'password_hint.required' => 'Gợi ý mật khẩu là bắt buộc khi đặt mật khẩu chương.',
                ]);
                $passwordHint = $request->password_hint;
                if ($request->filled('chapter_password')) {
                    $passwordEncrypted = Crypt::encryptString($request->chapter_password);
                }
            } else {
                $passwordEncrypted = null;
                $passwordHint = null;
            }

            $chapter->update([
                'title' => $request->title,
                'content' => $this->normalizeChapterContent($request->content),
                'number' => $request->number,
                'status' => $request->status,
                'is_free' => $isFree,
                'price' => $price,
                'slug' => $chapter->id . '-chuong' . $request->number,
                'published_at' => $publishedAt,
                'scheduled_publish_at' => $scheduledPublishAt,
                'password_encrypted' => $passwordEncrypted,
                'password_hint' => $passwordHint,
            ]);

            return redirect()->route('author.stories.chapters.index', $story)->with('success', 'Đã cập nhật chương ' . $request->number);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra.')->withInput();
        }
    }

    public function destroy(Story $story, Chapter $chapter)
    {
        $this->authorizeStory($story);

        $canDelete = $this->canDeleteChapter($chapter);
        if (!$canDelete['can_delete']) {
            return redirect()->back()->with('error', $canDelete['message']);
        }

        try {
            $chapter->delete();
            return redirect()->route('author.stories.chapters.index', $story)->with('success', 'Đã xóa chương.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra.');
        }
    }

    public function bulkCreate(Story $story)
    {
        $this->authorizeStory($story);
        return view('pages.author.chapter.bulk-create', compact('story'));
    }

    public function checkExisting(Request $request, Story $story)
    {
        $this->authorizeStory($story);
        $request->validate([
            'chapter_numbers' => 'required|array',
            'chapter_numbers.*' => 'integer|min:1',
        ], [
            'chapter_numbers.required' => 'Vui lòng gửi danh sách số chương cần kiểm tra.',
            'chapter_numbers.*.integer' => 'Mỗi số chương phải là số nguyên.',
            'chapter_numbers.*.min' => 'Số chương phải lớn hơn 0.',
        ], [
            'chapter_numbers' => 'Danh sách số chương',
        ]);

        $existingNumbers = $story->chapters()
            ->whereIn('number', $request->chapter_numbers)
            ->pluck('number')
            ->toArray();

        return response()->json([
            'existing' => $existingNumbers,
            'available' => array_values(array_diff($request->chapter_numbers, $existingNumbers)),
        ]);
    }

    public function bulkStore(Request $request, Story $story)
    {
        $this->authorizeStory($story);

        $validated = $request->validate([
            'chapters' => 'required|string',
        ], [
            'chapters.required' => 'Dữ liệu chương không được để trống.',
        ], [
            'chapters' => 'Dữ liệu chương (JSON)',
        ]);
        $chaptersData = json_decode($validated['chapters'], true);

        if (!is_array($chaptersData)) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ'], 400);
        }

        foreach ($chaptersData as $i => $ch) {
            if (empty($ch['number']) || empty($ch['title']) || empty($ch['content'])) {
                return response()->json(['success' => false, 'message' => "Chương thứ " . ($i + 1) . " thiếu dữ liệu"], 400);
            }
            if (!isset($ch['price']) || $ch['price'] < 0) {
                return response()->json(['success' => false, 'message' => "Chương thứ " . ($i + 1) . " có giá không hợp lệ"], 400);
            }
            if (empty($ch['publish_now']) && empty($ch['published_at'])) {
                return response()->json(['success' => false, 'message' => "Chương thứ " . ($i + 1) . " thiếu ngày công bố"], 400);
            }
        }

        $existingNumbers = $story->chapters()->whereIn('number', collect($chaptersData)->pluck('number'))->pluck('number')->toArray();
        $createdCount = 0;

        DB::beginTransaction();
        try {
            foreach ($chaptersData as $ch) {
                if (in_array($ch['number'], $existingNumbers)) continue;

                $publishNow = $ch['publish_now'] ?? false;
                $status = 'draft';
                $publishedAt = null;
                $scheduledPublishAt = null;

                if ($publishNow) {
                    $status = 'published';
                    $publishedAt = now();
                } elseif (!empty($ch['published_at'])) {
                    $scheduledTime = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $ch['published_at'], 'Asia/Ho_Chi_Minh');
                    if ($scheduledTime->gt(now())) {
                        $scheduledPublishAt = $scheduledTime;
                    } else {
                        $status = 'published';
                        $publishedAt = $scheduledTime;
                    }
                }

                $isZhihu = ($story->story_type ?? 'normal') === 'zhihu';
                $price = $isZhihu ? 0 : ($ch['price'] ?? 0);
                $chapter = $story->chapters()->create([
                    'number' => $ch['number'],
                    'title' => $ch['title'],
                    'content' => $this->normalizeChapterContent($ch['content'] ?? ''),
                    'price' => $price,
                    'is_free' => $isZhihu || $price == 0,
                    'status' => $status,
                    'slug' => 'temp-' . time(),
                    'published_at' => $publishedAt,
                    'scheduled_publish_at' => $scheduledPublishAt,
                    'user_id' => Auth::id(),
                ]);
                $chapter->update(['slug' => $chapter->id . '-chuong' . $ch['number']]);
                $createdCount++;
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => "Đã tạo {$createdCount} chương", 'created_count' => $createdCount]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function bulkEditPrice(Request $request, Story $story)
    {
        $this->authorizeStory($story);

        if (($story->story_type ?? 'normal') === 'zhihu') {
            return redirect()->route('author.stories.chapters.index', $story)
                ->with('info', 'Truyện Zhihu luôn miễn phí, không cần sửa giá.');
        }

        $query = $story->chapters()->orderBy('number', 'asc');
        if ($request->status) {
            $query->where('status', $request->status);
        }
        $chapters = $query->paginate(15)->withQueryString();
        $minChapter = $story->chapters()->min('number') ?? 1;
        $maxChapter = $story->chapters()->max('number') ?? 1;

        return view('pages.author.chapter.bulk-edit-price', compact('story', 'chapters', 'minChapter', 'maxChapter'));
    }

    public function bulkUpdatePrice(Request $request, Story $story)
    {
        $this->authorizeStory($story);

        if (($story->story_type ?? 'normal') === 'zhihu') {
            return redirect()->back()->with('error', 'Truyện Zhihu luôn miễn phí.');
        }

        $request->validate([
            'chapter_ids' => 'nullable|array',
            'chapter_ids.*' => 'exists:chapters,id',
            'from_chapter' => 'nullable|integer|min:1',
            'to_chapter' => 'nullable|integer|min:1',
            'action' => 'required|in:free,price',
            'price' => 'required_if:action,price|nullable|integer|min:0',
        ], [
            'chapter_ids.*.exists' => 'Chương không hợp lệ.',
            'from_chapter.integer' => 'Số chương từ phải là số nguyên.',
            'to_chapter.integer' => 'Số chương đến phải là số nguyên.',
            'action.required' => 'Vui lòng chọn hành động (Miễn phí hoặc Đặt giá).',
            'action.in' => 'Hành động không hợp lệ.',
            'price.required_if' => 'Vui lòng nhập giá khi đặt giá chương.',
            'price.integer' => 'Giá phải là số nguyên.',
            'price.min' => 'Giá không được nhỏ hơn 0.',
        ]);

        $chapters = collect();

        if ($request->filled('from_chapter') && $request->filled('to_chapter')) {
            $from = (int) $request->from_chapter;
            $to = (int) $request->to_chapter;
            if ($from > $to) {
                return redirect()->back()->with('error', 'Số chương từ phải nhỏ hơn hoặc bằng số chương đến.');
            }
            $chapters = $story->chapters()
                ->whereBetween('number', [$from, $to])
                ->get();
        } elseif ($request->filled('chapter_ids')) {
            $chapters = $story->chapters()->whereIn('id', $request->chapter_ids)->get();
        }

        if ($chapters->isEmpty()) {
            return redirect()->back()->with('error', 'Vui lòng chọn chương hoặc nhập khoảng từ chương đến chương.');
        }

        $isFree = $request->action === 'free';
        $price = $isFree ? 0 : (int) $request->price;

        $updated = 0;
        foreach ($chapters as $chapter) {
            $data = [
                'is_free' => $isFree,
                'price' => $price,
            ];
            if (!$isFree) {
                $data['password_encrypted'] = null;
                $data['password_hint'] = null;
            }
            $chapter->update($data);
            $updated++;
        }

        $msg = $isFree
            ? "Đã đặt miễn phí {$updated} chương."
            : "Đã cập nhật giá {$price} nấm cho {$updated} chương.";

        return redirect()->route('author.stories.chapters.index', $story)->with('success', $msg);
    }

    private function normalizeChapterContent(string $content): string
    {
        $content = preg_replace('/<\/?(b|strong|i|em)\b[^>]*>/i', '', $content ?? '');

        $content = strtr($content, [
            '**' => '',
            '__' => '',
            '*'  => '',
            '_'  => '',
        ]);

        $content = trim($content);
        if ($content === '') {
            return $content;
        }

        $paragraphs = preg_split('/\n+/u', $content, -1, PREG_SPLIT_NO_EMPTY);
        $paragraphs = array_map('trim', $paragraphs);
        $paragraphs = array_filter($paragraphs, fn ($p) => $p !== '');

        return implode("\n\n", $paragraphs);
    }

    private function canDeleteChapter(Chapter $chapter): array
    {
        if ($chapter->is_free) return ['can_delete' => true, 'message' => ''];
        if ($chapter->story->purchases()->exists()) return ['can_delete' => false, 'message' => 'Đã có người mua combo.'];
        if ($chapter->purchases()->exists()) return ['can_delete' => false, 'message' => 'Đã có người mua chương.'];
        return ['can_delete' => true, 'message' => ''];
    }
}
