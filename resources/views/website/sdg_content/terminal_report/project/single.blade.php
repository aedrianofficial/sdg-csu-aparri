@extends('layouts.website2')

@section('content')
    <div class="container">
        <!-- Terminal Report Section -->
        <div class="content-header">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">View Terminal Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('website.home') }}"><i class="fas fa-home"></i> Home</a>
                        </li>
                        <li class="breadcrumb-item active"><a href="{{ route('website.sdg_project_main') }}">All
                                Projects</a></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Terminal Report Content -->
            <div class="col-lg-8">
                <div class="card card-primary card-outline post">
                    <div class="card-header">
                        <h5 class="card-title m-0 text-truncate" title="{{ $terminalReport->related_title }}">
                            <i class="fas fa-file-alt"></i> {{ Str::limit($terminalReport->related_title, 40) }}
                        </h5>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <!-- Report Details -->
                        <div class="report-details mt-3">
                            <!-- Created At -->
                            <div class="report-detail-item mb-2">
                                <i class="fas fa-calendar-alt"></i> <strong>Logged On:</strong>
                                <span>{{ date('d M Y', strtotime($terminalReport->created_at)) }}</span>
                            </div>
                            <!-- Logged By -->
                            <div class="report-detail-item mb-2">
                                <i class="fas fa-user"></i> <strong>Logged By:</strong>
                                <span>{{ $terminalReport->user->first_name ?? 'N/A' }}
                                    {{ $terminalReport->user->last_name ?? 'N/A' }}</span>
                            </div>
                            <!-- Cooperating Agency -->
                            <div class="report-detail-item mb-2">
                                <i class="fas fa-building"></i> <strong>Cooperating Agency:</strong>
                                <span>{{ $terminalReport->cooperatingAgency->agency ?? 'N/A' }}</span>
                            </div>
                            <!-- Funding Agency -->
                            <div class="report-detail-item mb-2">
                                <i class="fas fa-dollar-sign"></i> <strong>Funding Agency:</strong>
                                <span>{{ $terminalReport->fundingAgency->agency ?? 'N/A' }}</span>
                            </div>
                            <!-- Researchers -->
                            <div class="report-detail-item mb-2">
                                <i class="fas fa-user-graduate"></i> <strong>Researchers:</strong>
                                <span>{{ implode(', ', $terminalReport->researchers->pluck('name')->unique()->toArray()) ?? 'N/A' }}</span>
                            </div>
                            <!-- Total Approved Budget -->
                            <div class="report-detail-item mb-2">
                                <i class="fas fa-money-bill-wave"></i> <strong>Total Approved Budget:</strong>
                                <span>{{ $terminalReport->total_approved_budget }}</span>
                            </div>
                            <!-- Actual Released Budget -->
                            <div class="report-detail-item mb-2">
                                <i class="fas fa-money-bill-wave"></i> <strong>Actual Released Budget:</strong>
                                <span>{{ $terminalReport->actual_released_budget }}</span>
                            </div>
                            <!-- Actual Expenditure -->
                            <div class="report-detail-item mb-2">
                                <i class="fas fa-money-bill-wave"></i> <strong>Actual Expenditure:</strong>
                                <span>{{ $terminalReport->actual_expenditure }}</span>
                            </div>
                            <!-- Abstract -->
                            <div class="report-detail-item mb-2">
                                <i class="fas fa-file-alt"></i> <strong>Abstract:</strong>
                                <p>{{ $terminalReport->abstract }}</p>
                            </div>
                            <!-- Related Link -->
                            <div class="report-detail-item mb-2">
                                <i class="fas fa-link"></i> <strong>Related Link:</strong>
                                <span>
                                    @if ($terminalReport->related_link && $terminalReport->related_link !== 'N/A')
                                        <a href="{{ $terminalReport->related_link }}"
                                            target="_blank">{{ $terminalReport->related_link }}</a>
                                    @else
                                        <span>N/A</span>
                                    @endif
                                </span>
                            </div>

                            <div class="mb-3">
                                <label for="file" class="form-label">Terminal Report File (View Only):</label>
                            
                                @if (!$terminalReportFile)
                                    <input type="text" name="file" id="file" class="form-control"
                                        value="No files available for this terminal report." readonly>
                                @else
                                    <div class="pdf-viewer-container"
                                        style="width: 100%; max-height: 80vh; border: 1px solid #ddd; overflow: auto; position: relative;">
                                        <div id="zoom-wrapper" 
                                            style="touch-action: pan-x pan-y; transform-origin: 0 0; min-width: 100%; min-height: 100%;">
                                            <div id="pdf-viewer"
                                                style="width: 100%; padding: 10px; box-sizing: border-box; display: flex; flex-direction: column; align-items: center;">
                                            </div>
                                        </div>
                                    </div>
                            
                                    <!-- Include PDF.js and Hammer.js -->
                                    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
                                    <script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js"></script>
                            
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';
                            
                                            const viewerContainer = document.getElementById('pdf-viewer');
                                            const zoomWrapper = document.getElementById('zoom-wrapper');
                                            const pdfContainer = document.querySelector('.pdf-viewer-container');
                                            const pdfUrl = "{{ route('terminal.report.file.view', $terminalReportFile->id) }}";
                            
                                            // Initial scale values
                                            let currentScale = 1;
                                            let lastPosX = 0;
                                            let lastPosY = 0;
                                            let isDragging = false;
                                            
                                            // Set initial higher scale for mobile devices
                                            if (window.innerWidth < 768) {
                                                currentScale = 1.5; // Start with higher scale on mobile
                                            }
                            
                                            pdfjsLib.getDocument(pdfUrl).promise.then(pdf => {
                                                const totalPages = pdf.numPages;
                            
                                                const renderPage = (pageNumber) => {
                                                    pdf.getPage(pageNumber).then(page => {
                                                        const containerWidth = viewerContainer.offsetWidth;
                                                        const unscaledViewport = page.getViewport({
                                                            scale: 1
                                                        });
                            
                                                        // Initial scale for fit-to-width
                                                        let scale = containerWidth / unscaledViewport.width;
                            
                                                        // Adjust for high-DPI screens (e.g., mobile, retina)
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
                            
                                                        // Improve sharpness on high DPI
                                                        context.setTransform(outputScale, 0, 0, outputScale, 0, 0);
                            
                                                        viewerContainer.appendChild(canvas);
                            
                                                        const renderContext = {
                                                            canvasContext: context,
                                                            viewport: viewport
                                                        };
                            
                                                        page.render(renderContext).promise.then(() => {
                                                            if (pageNumber < totalPages) {
                                                                renderPage(pageNumber + 1);
                                                            } else {
                                                                // Apply initial scale for mobile devices after all pages are rendered
                                                                if (window.innerWidth < 768) {
                                                                    zoomWrapper.style.transform = `scale(${currentScale})`;
                                                                    updateScrollability();
                                                                }
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
                            
                                            // Function to update container scrollability based on zoom level
                                            function updateScrollability() {
                                                if (currentScale > 1) {
                                                    pdfContainer.style.overflow = 'auto';
                                                    // Calculate new content size based on scale
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
                            
                                            // Set up pinch zoom with Hammer.js
                                            const hammer = new Hammer(zoomWrapper);
                                            hammer.get('pinch').set({ enable: true });
                                            hammer.get('pan').set({ direction: Hammer.DIRECTION_ALL });
                            
                                            // Handle pinch zoom
                                            hammer.on('pinchmove', function(ev) {
                                                const newScale = Math.min(Math.max(0.5, currentScale * ev.scale), 4);
                                                zoomWrapper.style.transform = `scale(${newScale})`;
                                            });
                            
                                            hammer.on('pinchend', function(ev) {
                                                currentScale = Math.min(Math.max(0.5, currentScale * ev.scale), 4);
                                                updateScrollability();
                                            });
                            
                                            // Double tap to zoom in/out
                                            let isZoomed = false;
                                            hammer.on('doubletap', function(ev) {
                                                isZoomed = !isZoomed;
                                                currentScale = isZoomed ? 2 : 1;
                                                zoomWrapper.style.transform = `scale(${currentScale})`;
                                                updateScrollability();
                                                
                                                // Center the zoom on tap position when zooming in
                                                if (isZoomed) {
                                                    const rect = pdfContainer.getBoundingClientRect();
                                                    const tapX = ev.center.x - rect.left;
                                                    const tapY = ev.center.y - rect.top;
                                                    
                                                    // Calculate scroll position to center on tap point
                                                    pdfContainer.scrollLeft = (tapX * 2) - (pdfContainer.clientWidth / 2);
                                                    pdfContainer.scrollTop = (tapY * 2) - (pdfContainer.clientHeight / 2);
                                                }
                                            });
                            
                                            // Allow drag to scroll when zoomed in
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
                            
                                            // Handle window resize
                                            window.addEventListener('resize', function() {
                                                // Reset zoom and recalculate on orientation change
                                                if (currentScale > 1) {
                                                    updateScrollability();
                                                }
                                            });
                                        });
                                    </script>
                                @endif
                            </div>
                            <!-- Meta Info -->
                            <div class="post-meta mt-4">
                                <ul class="list-unstyled">
                                    <li>
                                        <strong><i class="fas fa-tags"></i> Related Project:</strong>
                                        <a
                                            href="{{ route('website.display_single_project', $terminalReport->related_id) }}">
                                            {{ $terminalReport->related_title }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SDGs Section - Moved outside the terminal report content div -->
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
                                    <a href="{{ route('website.display_project_sdg', $singleSdg->id) }}" class="nav-link">
                                        {{ $singleSdg->name }}
                                        @php
                                            $badgeColor = 'bg-primary';
                                            if ($singleSdg->project_count == 0) {
                                                $badgeColor = 'bg-danger';
                                            } elseif (
                                                $singleSdg->project_count >= 1 &&
                                                $singleSdg->project_count < 10
                                            ) {
                                                $badgeColor = 'bg-warning';
                                            } elseif (
                                                $singleSdg->project_count >= 10 &&
                                                $singleSdg->project_count < 20
                                            ) {
                                                $badgeColor = 'bg-primary';
                                            } elseif ($singleSdg->project_count >= 20) {
                                                $badgeColor = 'bg-success';
                                            }
                                        @endphp
                                        <span class="float-right badge {{ $badgeColor }}">
                                            {{ $singleSdg->project_count }}
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
