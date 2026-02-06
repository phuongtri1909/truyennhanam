<?php

namespace App\Http\Controllers\Client;

use App\Models\Story;
use App\Models\Chapter;
use App\Models\Category;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

class SitemapController extends Controller
{
    const SITEMAP_PER_PAGE = 500;

    public function index()
    {
        $sitemaps = [];

        $sitemaps[] = [
            'url' => route('sitemap.main'),
            'lastmod' => Carbon::now()->toAtomString(),
        ];
        $sitemaps[] = [
            'url' => route('sitemap.categories'),
            'lastmod' => Category::latest('updated_at')->first()?->updated_at?->toAtomString() ?? Carbon::now()->toAtomString(),
        ];

        $storiesQuery = Story::where('status', 'published')->visible()->where('is_18_plus', false);
        $storiesCount = $storiesQuery->count();
        $storiesPages = $storiesCount > 0 ? (int) ceil($storiesCount / self::SITEMAP_PER_PAGE) : 0;
        $storiesLastmod = (clone $storiesQuery)->latest('updated_at')->first()?->updated_at?->toAtomString() ?? Carbon::now()->toAtomString();
        for ($p = 1; $p <= $storiesPages; $p++) {
            $sitemaps[] = [
                'url' => route('sitemap.stories', ['page' => $p]),
                'lastmod' => $storiesLastmod,
            ];
        }

        $chaptersQuery = Chapter::where('status', 'published')
            ->whereHas('story', fn ($q) => $q->where('hide', false)->where('is_18_plus', false));
        $chaptersCount = $chaptersQuery->count();
        $chaptersPages = $chaptersCount > 0 ? (int) ceil($chaptersCount / self::SITEMAP_PER_PAGE) : 0;
        $chaptersLastmod = (clone $chaptersQuery)->latest('updated_at')->first()?->updated_at?->toAtomString() ?? Carbon::now()->toAtomString();
        for ($p = 1; $p <= $chaptersPages; $p++) {
            $sitemaps[] = [
                'url' => route('sitemap.chapters', ['page' => $p]),
                'lastmod' => $chaptersLastmod,
            ];
        }

        return response()->view('sitemaps.index', [
            'sitemaps' => $sitemaps,
        ])->header('Content-Type', 'text/xml');
    }

    public function main()
    {
        $routes = [
            [
                'loc' => route('home'),
                'lastmod' => Carbon::now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '1.0'
            ],
            [
                'loc' => route('login'),
                'lastmod' => Carbon::now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.3'
            ],
            [
                'loc' => route('register'),
                'lastmod' => Carbon::now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.3'
            ]
        ];

        return response()->view('sitemaps.main', [
            'routes' => $routes,
        ])->header('Content-Type', 'text/xml');
    }

    public function stories(int $page = 1)
    {
        $page = max(1, $page);
        $stories = Story::where('status', 'published')
            ->visible()
            ->where('is_18_plus', false)
            ->select('id', 'slug', 'updated_at')
            ->latest('updated_at')
            ->offset(($page - 1) * self::SITEMAP_PER_PAGE)
            ->limit(self::SITEMAP_PER_PAGE)
            ->get();

        return response()->view('sitemaps.stories', [
            'stories' => $stories,
        ])->header('Content-Type', 'text/xml');
    }

    public function chapters(int $page = 1)
    {
        $page = max(1, $page);
        $chapters = Chapter::where('status', 'published')
            ->whereHas('story', fn ($q) => $q->where('hide', false)->where('is_18_plus', false))
            ->select('id', 'story_id', 'slug', 'updated_at')
            ->with(['story:id,slug'])
            ->latest('updated_at')
            ->offset(($page - 1) * self::SITEMAP_PER_PAGE)
            ->limit(self::SITEMAP_PER_PAGE)
            ->get();

        return response()->view('sitemaps.chapters', [
            'chapters' => $chapters,
        ])->header('Content-Type', 'text/xml');
    }

    public function categories()
    {
        $categories = Category::select('id', 'slug', 'updated_at')
            ->get();

        return response()->view('sitemaps.categories', [
            'categories' => $categories,
        ])->header('Content-Type', 'text/xml');
    }
}