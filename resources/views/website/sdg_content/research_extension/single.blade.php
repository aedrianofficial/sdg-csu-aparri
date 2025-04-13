@extends('layouts.website2')

@section('styles')
    <style>
        #map {
            height: 400px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .leaflet-popup .fas {
            color: #007bff;
        }

        .quill-content p {
            margin-bottom: 0;
            line-height: 1.5;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <!-- Research Section -->
        <div class="content-header">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $research->title }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('website.home') }}"><i class="fas fa-home"></i> Home</a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('website.sdg_research_main') }}"></a>All
                            Research</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Research Content -->
            <div class="col-lg-8">
                <div class="card card-secondary card-outline post">
                    <div class="card-header">
                        <h5 class="card-title m-0 text-truncate" title="{{ $research->title }}">
                            <i class="fas fa-book"></i> {{ Str::limit($research->title, 40) }}
                        </h5>
                    </div>
                    <div class="card-body d-flex flex-column">

                        <!-- Research Details -->
                        <div class="research-details mt-3">
                            <!-- Created At -->
                            <div class="research-detail-item mb-2">
                                <i class="fas fa-calendar-alt"></i> <strong>Created On:</strong>
                                <span>{{ date('d M Y', strtotime($research->created_at)) }}</span>
                            </div>

                            <!-- Description -->
                            <div class="research-detail-item mb-2">
                                <i class="fas fa-align-left"></i> <strong>Description:</strong>
                                <span class="quill-content" style="color: black;">{!! $research->description !!}</span>
                            </div>

                            <!-- Research File Viewer -->
                            <!-- Research File Viewer -->
                            <div class="mb-3">
                                <label for="file" class="form-label">Research File (View Only):</label>

                                @if ($research->researchfiles->isEmpty())
                                    <input type="text" name="file" id="file" class="form-control"
                                        value="No files available for this research." readonly>
                                @else
                                    @php
                                        $firstResearchFile = $research->researchfiles->first(); // First file, add loop if needed
                                    @endphp

                                    <div class="pdf-viewer-container"
                                        style="width: 100%; max-height: 80vh; border: 1px solid #ddd; overflow: auto; position: relative;">
                                        <div id="research-zoom-wrapper"
                                            style="touch-action: pan-x pan-y; transform-origin: 0 0; min-width: 100%; min-height: 100%;">
                                            <div id="research-pdf-viewer"
                                                style="width: 100%; padding: 10px; box-sizing: border-box; display: flex; flex-direction: column; align-items: center;">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Include PDF.js and Hammer.js -->
                                    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
                                    <script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js"></script>

                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            pdfjsLib.GlobalWorkerOptions.workerSrc =
                                                'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';

                                            const viewerContainer = document.getElementById('research-pdf-viewer');
                                            const zoomWrapper = document.getElementById('research-zoom-wrapper');
                                            const pdfContainer = document.querySelector('.pdf-viewer-container');
                                            const pdfUrl = "{{ route('research.file.view', $firstResearchFile->id) }}";

                                            let currentScale = 1;
                                            let lastPosX = 0;
                                            let lastPosY = 0;
                                            let isDragging = false;

                                            if (window.innerWidth < 768) {
                                                currentScale = 1.5;
                                            }

                                            pdfjsLib.getDocument(pdfUrl).promise.then(pdf => {
                                                const totalPages = pdf.numPages;

                                                const renderPage = (pageNumber) => {
                                                    pdf.getPage(pageNumber).then(page => {
                                                        const containerWidth = viewerContainer.offsetWidth;
                                                        const unscaledViewport = page.getViewport({
                                                            scale: 1
                                                        });
                                                        let scale = containerWidth / unscaledViewport.width;
                                                        const outputScale = window.devicePixelRatio || 1;
                                                        const viewport = page.getViewport({
                                                            scale
                                                        });

                                                        const canvas = document.createElement('canvas');
                                                        const context = canvas.getContext('2d');

                                                        canvas.width = viewport.width * outputScale;
                                                        canvas.height = viewport.height * outputScale;
                                                        canvas.style.width = `${viewport.width}px`;
                                                        canvas.style.height = `${viewport.height}px`;
                                                        canvas.className = 'shadow-sm rounded';

                                                        context.setTransform(outputScale, 0, 0, outputScale, 0, 0);

                                                        viewerContainer.appendChild(canvas);

                                                        const renderContext = {
                                                            canvasContext: context,
                                                            viewport: viewport
                                                        };

                                                        page.render(renderContext).promise.then(() => {
                                                            if (pageNumber < totalPages) {
                                                                renderPage(pageNumber + 1);
                                                            } else if (window.innerWidth < 768) {
                                                                zoomWrapper.style.transform = `scale(${currentScale})`;
                                                                updateScrollability();
                                                            }
                                                        });
                                                    });
                                                };

                                                renderPage(1);
                                            }).catch(error => {
                                                console.error("PDF load error:", error);
                                                viewerContainer.innerHTML =
                                                    '<div class="text-danger p-3">Failed to load PDF. Please try again later.</div>';
                                            });

                                            function updateScrollability() {
                                                if (currentScale > 1) {
                                                    pdfContainer.style.overflow = 'auto';
                                                    const newWidth = viewerContainer.offsetWidth * currentScale;
                                                    const newHeight = viewerContainer.offsetHeight * currentScale;
                                                    zoomWrapper.style.width = `${newWidth}px`;
                                                    zoomWrapper.style.height = `${newHeight}px`;
                                                } else {
                                                    pdfContainer.style.overflow = 'auto';
                                                    zoomWrapper.style.width = '100%';
                                                    zoomWrapper.style.height = '100%';
                                                }
                                            }

                                            const hammer = new Hammer(zoomWrapper);
                                            hammer.get('pinch').set({
                                                enable: true
                                            });
                                            hammer.get('pan').set({
                                                direction: Hammer.DIRECTION_ALL
                                            });

                                            hammer.on('pinchmove', function(ev) {
                                                const newScale = Math.min(Math.max(0.5, currentScale * ev.scale), 4);
                                                zoomWrapper.style.transform = `scale(${newScale})`;
                                            });

                                            hammer.on('pinchend', function(ev) {
                                                currentScale = Math.min(Math.max(0.5, currentScale * ev.scale), 4);
                                                updateScrollability();
                                            });

                                            let isZoomed = false;
                                            hammer.on('doubletap', function(ev) {
                                                isZoomed = !isZoomed;
                                                currentScale = isZoomed ? 2 : 1;
                                                zoomWrapper.style.transform = `scale(${currentScale})`;
                                                updateScrollability();

                                                if (isZoomed) {
                                                    const rect = pdfContainer.getBoundingClientRect();
                                                    const tapX = ev.center.x - rect.left;
                                                    const tapY = ev.center.y - rect.top;
                                                    pdfContainer.scrollLeft = (tapX * 2) - (pdfContainer.clientWidth / 2);
                                                    pdfContainer.scrollTop = (tapY * 2) - (pdfContainer.clientHeight / 2);
                                                }
                                            });

                                            hammer.on('panstart', function(ev) {
                                                if (currentScale > 1) {
                                                    isDragging = true;
                                                    lastPosX = pdfContainer.scrollLeft;
                                                    lastPosY = pdfContainer.scrollTop;
                                                }
                                            });

                                            hammer.on('panmove', function(ev) {
                                                if (isDragging && currentScale > 1) {
                                                    pdfContainer.scrollLeft = lastPosX - ev.deltaX;
                                                    pdfContainer.scrollTop = lastPosY - ev.deltaY;
                                                }
                                            });

                                            hammer.on('panend', function() {
                                                isDragging = false;
                                            });

                                            window.addEventListener('resize', function() {
                                                if (currentScale > 1) {
                                                    updateScrollability();
                                                }
                                            });
                                        });
                                    </script>
                                @endif
                            </div>


                            <div class="research-detail-item mb-2">
                                <strong><i class="fas fa-file"></i> Full version File:</strong>
                                @if ($research->file_link)
                                    <a href="{{ $research->file_link }}" target="_blank">{{ $research->file_link }}</a>
                                @else
                                    <p>No full version file link available.</p>
                                @endif
                            </div>





                        </div>

                        <!-- Meta Info -->
                        <div class="post-meta mt-4">
                            <ul class="list-unstyled">
                                <li>
                                    <i class="fas fa-calendar-day"></i>
                                    {{ date('d M Y', strtotime($research->created_at)) }}
                                </li>
                                <li>
                                    <strong><i class="fas fa-tags"></i> SDGs:</strong>
                                    @foreach ($research->sdg as $research_sdgs)
                                        @php
                                            $badgeColors = [
                                                1 => 'badge-success',
                                                2 => 'badge-info',
                                                3 => 'badge-warning',
                                                4 => 'badge-danger',
                                                5 => 'badge-primary',
                                                6 => 'badge-secondary',
                                                7 => 'badge-light',
                                                8 => 'badge-dark',
                                                9 => 'badge-info',
                                                10 => 'badge-warning',
                                                11 => 'badge-danger',
                                                12 => 'badge-primary',
                                                13 => 'badge-success',
                                                14 => 'badge-light',
                                                15 => 'badge-warning',
                                                16 => 'badge-danger',
                                                17 => 'badge-primary',
                                            ];
                                            $badgeColor = $badgeColors[$research_sdgs->id] ?? 'badge-secondary';
                                        @endphp

                                        <span class="badge {{ $badgeColor }}">{{ $research_sdgs->name }}</span>
                                    @endforeach
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- SDG Sub Categories -->
                <div class="card card-primary card-outline mt-4">
                    <div class="card-header">
                        <h5 class="card-title m-0">SDG Targets:</h5>
                    </div>
                    <div class="card-body">
                        @if ($research->sdgSubCategories->isEmpty())
                            <p>No SDG Targets available.</p>
                        @else
                            <ul class="list-unstyled">
                                @foreach ($research->sdgSubCategories as $subCategory)
                                    <li>
                                        <strong>{{ $subCategory->sub_category_name }}:</strong>
                                        <span>{{ $subCategory->sub_category_description }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        <p class="mt-2">
                            Source: <a
                                href="https://sustainabledevelopment.un.org/content/documents/11803Official-List-of-Proposed-SDG-Indicators.pdf"
                                target="_blank">https://sustainabledevelopment.un.org/content/documents/11803Official-List-of-Proposed-SDG-Indicators.pdf</a>
                        </p>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <h3>Related Reports</h3>

                        @if ($statusReports->isEmpty() && $terminalReports->isEmpty())
                            <div class="alert">
                                <strong class="text-danger">No related reports available.</strong>
                            </div>
                        @else
                            <div class="row">
                                @foreach ($statusReports as $statusReport)
                                    <div class="col-lg-3 col-6">
                                        <div
                                            class="small-box 
                                            @if ($statusReport->log_status == 'Proposed') bg-info 
                                            @elseif($statusReport->log_status == 'On-Going') bg-primary 
                                            @elseif($statusReport->log_status == 'On-Hold') bg-warning 
                                            @elseif($statusReport->log_status == 'Rejected') bg-danger @endif">
                                            <div class="inner">
                                                <br>
                                                <p>{{ $statusReport->log_status }}</p>
                                            </div>
                                            <div class="icon">
                                                <i
                                                    class="fas 
                                                    @if ($statusReport->log_status == 'Proposed') fa-lightbulb 
                                                    @elseif($statusReport->log_status == 'On-Going') fa-spinner 
                                                    @elseif($statusReport->log_status == 'On-Hold') fa-pause 
                                                    @elseif($statusReport->log_status == 'Rejected') fa-times-circle @endif"></i>
                                            </div>
                                            <a href="{{ route('website.status_reports.show_research_published', $statusReport->id) }}"
                                                class="small-box-footer"> More info <i
                                                    class="fas fa-arrow-circle-right"></i></a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="row">
                                @foreach ($terminalReports as $terminalReport)
                                    <div class="col-lg-3 col-6">
                                        <div class="small-box bg-success">
                                            <div class="inner">
                                                <br>
                                                <p>Completed</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                            <a href="{{ route('website.terminal_reports.show_research_published', $terminalReport->id) }}"
                                                class="small-box-footer"> More info <i
                                                    class="fas fa-arrow-circle-right"></i></a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- SDGs Section -->
            <div class="col-lg-4">
                <div class="card card-widget card-danger card-outline">
                    <div class="card-header">
                        <h5 class="card-title m-0 text-truncate"><i class="fas fa-bullseye"></i> Sustainable Development
                            Goals</h5>
                    </div>
                    <div class="card-footer p-0">
                        <ul class="nav flex-column">
                            @foreach ($sdgs as $singleSdg)
                                <li class="nav-item">
                                    <a href="{{ route('website.display_research_sdg', $singleSdg->id) }}" class="nav-link">
                                        {{ $singleSdg->name }}
                                        @php
                                            $badgeColor = 'bg-primary';
                                            if ($singleSdg->research_count == 0) {
                                                $badgeColor = 'bg-danger';
                                            } elseif ($singleSdg->research_count >= 1) {
                                                $badgeColor = 'bg-warning';
                                            } elseif ($singleSdg->research_count >= 10) {
                                                $badgeColor = 'bg-primary';
                                            } elseif ($singleSdg->research_count >= 20) {
                                                $badgeColor = 'bg-success';
                                            }
                                        @endphp
                                        <span class="float-right badge {{ $badgeColor }}">
                                            {{ $singleSdg->research_count }}
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Research Categories Section -->
                <div class="card card-widget card-info card-outline mt-3">
                    <div class="card-header">
                        <h5 class="card-title m-0 text-truncate"><i class="fas fa-tags"></i> Research Categories</h5>
                    </div>
                    <div class="card-footer p-0">
                        <ul class="nav flex-column">
                            @foreach ($researchCategories as $category)
                                <li class="nav-item">
                                    <a href="{{ route('website.display_research_category', $category->id) }}"
                                        class="nav-link">
                                        {{ $category->name }}
                                        @php
                                            $badgeColor = 'bg-primary';
                                            if ($category->research_count == 0) {
                                                $badgeColor = 'bg-danger';
                                            } elseif ($category->research_count >= 1) {
                                                $badgeColor = 'bg-warning';
                                            } elseif ($category->research_count >= 10) {
                                                $badgeColor = 'bg-primary';
                                            } elseif ($category->research_count >= 20) {
                                                $badgeColor = 'bg-success';
                                            }
                                        @endphp
                                        <span class="float-right badge {{ $badgeColor }}">
                                            {{ $category->research_count }}
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
