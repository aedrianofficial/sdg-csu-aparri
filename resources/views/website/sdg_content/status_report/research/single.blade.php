@extends('layouts.website2')

@section('content')
    <div class="container">
        <!-- Status Report Section -->
        <div class="content-header">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">View Status Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('website.home') }}"><i class="fas fa-home"></i> Home</a>
                        </li>
                        <li class="breadcrumb-item active"><a href="{{ route('website.sdg_research_main') }}">All
                                Research</a></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Status Report Content -->
            <div class="col-lg-8">
                <div class="card card-primary card-outline post">
                    <div class="card-header">
                        <h5 class="card-title m-0 text-truncate" title="{{ $statusReport->related_title }}">
                            <i class="fas fa-file-alt"></i> {{ Str::limit($statusReport->related_title, 40) }}
                        </h5>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <!-- Report Details -->
                        <div class="report-details mt-3">
                            <!-- Created At -->
                            <div class="report-detail-item mb-2">
                                <i class="fas fa-calendar-alt"></i> <strong>Logged On:</strong>
                                <span>{{ date('d M Y', strtotime($statusReport->created_at)) }}</span>
                            </div>
                            <!-- Logged By -->
                            <div class="report-detail-item mb-2">
                                <i class="fas fa-user"></i> <strong>Logged By:</strong>
                                <span>{{ $statusReport->loggedBy->first_name ?? 'N/A' }}
                                    {{ $statusReport->loggedBy->last_name ?? 'N/A' }}</span>
                            </div>
                            <!-- Remarks -->
                            <div class="report-detail-item mb-2">
                                <i class="fas fa-comment-dots"></i> <strong>Remarks:</strong>
                                <p>{{ $statusReport->remarks ?? 'N/A' }}</p>
                            </div>

                            <!-- Status -->
                            <div class="report-detail-item mb-2">
                                <i class="fas fa-check-circle"></i> <strong>Status:</strong>
                                <span>{{ $statusReport->log_status }}</span>
                            </div>

                            <!-- Related Link -->
                            <div class="report-detail-item mb-2">
                                <i class="fas fa-link"></i> <strong>Related Link:</strong>
                                <span>
                                    @if ($statusReport->related_link && $statusReport->related_link !== 'N/A')
                                        <a href="{{ $statusReport->related_link }}"
                                            target="_blank">{{ $statusReport->related_link }}</a>
                                    @else
                                        <span>N/A</span>
                                    @endif
                                </span>
                            </div>
                            <!-- Status Report Files -->
                            <div class="report-detail-item mb-2">
                                <i class="fas fa-file"></i> <strong>Files:</strong>
                                @if ($statusReport->statusReportFiles->isEmpty())
                                    <span>No files available for this status report.</span>
                                @else
                                    <ul class="list-unstyled">
                                        @foreach ($statusReport->statusReportFiles as $file)
                                            <li>
                                                <a href="{{ route('status.report.file.download', $file->id) }}"
                                                    target="_blank" rel="noopener noreferrer">
                                                    {{ $file->original_filename ?? 'status_report_file' }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>

                        <!-- Meta Info -->
                        <div class="post-meta mt-4">
                            <ul class="list-unstyled">

                                <li>
                                    <strong><i class="fas fa-tags"></i> Related Research:</strong>
                                    <a href="{{ route('website.display_single_project', $statusReport->related_id) }}">
                                        {{ $statusReport->related_title }}
                                    </a>
                                </li>
                            </ul>
                        </div>
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
                                    </ li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
