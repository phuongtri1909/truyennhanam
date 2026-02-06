<section class="truyen-de-cu-section mt-5 pt-3 pt-md-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="section-title-tdc d-flex align-items-center gap-2 m-0 fw-bold font-svn-apple color-3">
            <span class="section-title-bar"></span>
            Truyện đề cử
        </h2>
    </div>

    <div class="tdc-grid">
        @forelse ($hotStories as $story)
            <div class="tdc-grid-item">
                @include('components.stories-grid', ['story' => $story])
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center py-4 mb-4">
                    <i class="fas fa-book-open fa-2x mb-3 text-muted"></i>
                    <h5 class="mb-1">Không tìm thấy truyện nào</h5>
                    <p class="text-muted mb-0">Hiện không có truyện nào trong danh mục này.</p>
                </div>
            </div>
        @endforelse
    </div>
</section>

@once
    @push('styles')
        <style>
            .section-title-bar {
                width: 4px;
                height: 1.25em;
                background: var(--primary-color-2);
                border-radius: 2px;
            }

            .section-title-tdc {
                font-size: 1.25rem;
            }

            .section-link-tdc {
                font-size: 0.95rem;
            }

            .tdc-grid {
                display: grid;
                gap: 1rem;
                grid-template-columns: repeat(4, 1fr);
            }

            @media (min-width: 576px) {
                .tdc-grid {
                    grid-template-columns: repeat(6, 1fr);
                }
            }

            @media (min-width: 768px) {
                .tdc-grid {
                    grid-template-columns: repeat(6, 1fr);
                }
            }

            @media (min-width: 992px) {
                .tdc-grid {
                    grid-template-columns: repeat(8, 1fr);
                    gap: 1.25rem;
                }

                .section-title-tdc {
                    font-size: 1.5rem;
                }
            }

            .tdc-grid-item {
                min-width: 0;
                overflow: visible;
            }

            body.dark-mode .section-title-bar {
                background: var(--primary-color-7);
            }

            body.dark-mode .alert-info {
                background-color: rgba(13, 202, 240, 0.2) !important;
                border-color: #0dcaf0 !important;
                color: #0dcaf0 !important;
            }
        </style>
    @endpush
@endonce
