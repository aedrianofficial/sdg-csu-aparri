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
                        <li class="breadcrumb-item"><a href="{{ route('website.home2') }}"><i class="fas fa-home"></i> Home</a>
                        </li>
                        <li class="breadcrumb-item active"><a href="{{ route('website.sdg_research_main2') }}">All
                                Researchs</a></li>
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
                                        style="width: 100%; max-height: 80vh; border: 1px solid #ddd; overflow-y: auto;">
                                        <div id="pdf-viewer"
                                            style="width: 100%; padding: 10px; box-sizing: border-box; display: flex; flex-direction: column; align-items: center;">
                                        </div>
                                    </div>

                                    <!-- Include PDF.js -->
                                    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
                                    <script>
                                        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';

                                        const viewerContainer = document.getElementById('pdf-viewer');
                                        const pdfUrl = "{{ route('terminal.report.file.view', $terminalReportFile->id) }}";

                                        pdfjsLib.getDocument(pdfUrl).promise.then(pdf => {
                                            const totalPages = pdf.numPages;

                                            const renderPage = (pageNumber) => {
                                                pdf.getPage(pageNumber).then(page => {
                                                    const containerWidth = viewerContainer.offsetWidth;
                                                    const viewport = page.getViewport({
                                                        scale: 1
                                                    });
                                                    const scale = containerWidth / viewport.width;
                                                    const scaledViewport = page.getViewport({
                                                        scale
                                                    });

                                                    const canvas = document.createElement('canvas');
                                                    canvas.style.marginBottom = '15px';
                                                    canvas.style.maxWidth = '100%';
                                                    canvas.className = 'shadow-sm rounded';

                                                    const context = canvas.getContext('2d');
                                                    canvas.width = scaledViewport.width;
                                                    canvas.height = scaledViewport.height;

                                                    viewerContainer.appendChild(canvas);

                                                    const renderContext = {
                                                        canvasContext: context,
                                                        viewport: scaledViewport
                                                    };

                                                    page.render(renderContext).promise.then(() => {
                                                        if (pageNumber < totalPages) {
                                                            renderPage(pageNumber + 1);
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
                                    </script>
                                @endif
                            </div>


                            <!-- Meta Info -->
                            <div class="post-meta mt-4">
                                <ul class="list-unstyled">
                                    <li>
                                        <strong><i class="fas fa-tags"></i> Related Research:</strong>
                                        <a
                                            href="{{ route('website.display_single_research2', $terminalReport->related_id) }}">
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
                                    <a href="{{ route('website.display_research_sdg2', $singleSdg->id) }}" class="nav-link">
                                        {{ $singleSdg->name }}
                                        @php
                                            $badgeColor = 'bg-primary';
                                            if ($singleSdg->research_count == 0) {
                                                $badgeColor = 'bg-danger';
                                            } elseif (
                                                $singleSdg->research_count >= 1 &&
                                                $singleSdg->research_count < 10
                                            ) {
                                                $badgeColor = 'bg-warning';
                                            } elseif (
                                                $singleSdg->research_count >= 10 &&
                                                $singleSdg->research_count < 20
                                            ) {
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
            </div>
        </div>
    </div>
@endsection
