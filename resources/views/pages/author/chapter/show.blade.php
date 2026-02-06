@extends('layouts.information')

@section('info_title', 'Chương ' . $chapter->number)
@section('info_section_title', 'Chương ' . $chapter->number . ': ' . $chapter->title)
@section('info_section_desc', $story->title)

@section('info_content')
<div class="author-application-form-wrapper author-story-compact">
    <div class="author-form-card">
    <div class="mb-4">
        <a href="{{ route('author.stories.chapters.edit', [$story, $chapter]) }}" class="btn author-form-submit-btn btn-sm me-2">
            <i class="fa-solid fa-pen me-1"></i> Sửa chương
        </a>
        <a href="{{ route('author.stories.chapters.index', $story) }}" class="btn btn-outline-secondary btn-sm">Quay lại</a>
    </div>
    <h6 class="author-form-section-title"><i class="fa-solid fa-book-open me-2"></i> {{ $chapter->title }}</h6>
    <p class="author-form-info-text mb-3">
        Chương {{ $chapter->number }} • {{ $chapter->is_free ? 'Miễn phí' : $chapter->price . ' cám' }} • 
        <span class="badge author-status-tag bg-{{ $chapter->status == 'published' ? 'success' : 'secondary' }}">
            {{ $chapter->status == 'published' ? 'Hiển thị' : 'Nháp' }}
        </span>
    </p>
    <div class="border-top pt-3 mt-3 author-form-info-text" style="white-space: pre-wrap;">{!! $chapter->content !!}</div>
    </div>
</div>
@endsection
