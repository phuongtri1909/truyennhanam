@extends('layouts.app')
@section('content')
    @include('components.toast')
    {{-- @include('components.banner_home') --}}
    <section class="container-xl">
        @include('components.list_story_home', ['hotStories' => $hotStories])

        <div class="row mt-5">
            <div class="col-12 col-md-3">
                @include('components.list_story_new_chapter', [
                    'latestUpdatedStories' => $latestUpdatedStories,
                ])
            </div>
            <div class="col-12 col-md-9">
                @include('components.hot_stories')
            </div>
        </div>

        @if ($completedStories->count() > 0)
            @include('components.list_story_full', ['completedStories' => $completedStories])
        @endif

        @if (isset($zhihuStories) && $zhihuStories->count() > 0)
            @include('components.list_story_free', ['zhihuStories' => $zhihuStories])
        @endif

        @if (isset($categories) && $categories->count() > 0)
            @include('components.list_categories', ['categories' => $categories])
        @endif

    </section>
@endsection
