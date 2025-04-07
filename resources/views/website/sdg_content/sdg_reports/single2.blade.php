@extends('layouts.website2')
@section('styles')
    <style>
        .quill-content p {
            margin-bottom: 0;
            line-height: 1.5;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <!-- Reports Section -->
        <div class="content-header">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $report->title }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('website.home2') }}"><i class="fas fa-home"></i> Home</a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('website.sdg_report_main2') }}">All Reports</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Report Content -->
            <div class="col-lg-8">
                <div class="card card-success card-outline post">
                    <div class="card-header">
                        <h5 class="card-title m-0 text-truncate" title="{{ $report->title }}">
                            <i class="fas fa-file-alt"></i> {{ Str::limit($report->title, 40) }}
                        </h5>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <!-- Report Image -->

                        <img src="{{ $report->reportimg->image }}" class="card-img-top" alt="Report Image"
                            style="height: 300px; object-fit: cover;">



                        <!-- Report Details -->
                        <div class="report-details mt-3">
                            <!-- Related Project/Research -->
                            <div class="report-detail-item mb-2">
                                <i class="fas fa-link"></i> <strong>Related to:</strong>
                                <span>{{ $report->related_title }} ({{ ucfirst($report->related_type) }})</span>
                            </div>

                            <!-- Created At -->
                            <div class="report-detail-item mb-2">
                                <i class="fas fa-calendar-alt"></i> <strong>Created On:</strong>
                                <span>{{ date('d M Y', strtotime($report->created_at)) }}</span>
                            </div>

                            <!-- Description -->
                            <div class="report-detail-item mb-2">
                                <i class="fas fa-align-left"></i> <strong>Description:</strong>
                                <span class="quill-content" style="color: black;">{!! $report->description !!}</span>
                            </div>


                        </div>

                        <!-- Meta Info -->
                        <div class="post-meta mt-4">
                            <ul class="list-unstyled">
                                <li>
                                    <i class="fas fa-calendar-day"></i> {{ date('d M Y', strtotime($report->created_at)) }}
                                </li>
                                <li>
                                    <strong><i class="fas fa-tags"></i> SDGs:</strong>
                                    @foreach ($report->sdg as $report_sdgs)
                                        @php
                                            // Assign different badge colors based on SDG name or ID
                                            $badgeColors = [
                                                1 => 'badge-success', // SDG 1 - Green
                                                2 => 'badge-info', // SDG 2 - Light Blue
                                                3 => 'badge-warning', // SDG 3 - Yellow
                                                4 => 'badge-danger', // SDG 4 - Red
                                                5 => 'badge-primary', // SDG 5 - Blue
                                                6 => 'badge-secondary', // SDG 6 - Dark Gray
                                                7 => 'badge-success', // SDG 7 - Light Gray
                                                8 => 'badge-info', // SDG 1 - Green
                                                9 => 'badge-warning', // SDG 2 - Light Blue
                                                10 => 'badge-danger', // SDG 3 - Yellow
                                                11 => 'badge-primary', // SDG 4 - Red
                                                12 => 'badge-secondary', // SDG 5 - Blue
                                                13 => 'badge-success', // SDG 6 - Dark Gray
                                                14 => 'badge-info', // SDG 7 - Light Gray
                                                15 => 'badge-warning', // SDG 1 - Green
                                                16 => 'badge-danger', // SDG 2 - Light Blue
                                                17 => 'badge-primary', // SDG 3 - Yellow
                                            ];

                                            // Default badge color if no specific match
                                            $badgeColor = $badgeColors[$report_sdgs->id] ?? 'badge-secondary';
                                        @endphp

                                        <span class="badge {{ $badgeColor }}">{{ $report_sdgs->name }}</span>
                                    @endforeach
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-lg-12">
                        <h3>Related Project</h3>
                        @if ($projects->isNotEmpty())
                            <div class="row">
                                @foreach ($projects as $project)
                                    <div class="col-lg-6 mb-4"> <!-- Column for each card -->
                                        <div class="card card-primary card-outline h-100 d-flex flex-column post">
                                            <div class="card-header">
                                                <h5 class="card-title m-0 text-truncate" title="{{ $project->title }}">
                                                    <i class="fas fa-project-diagram"></i>
                                                    {{ Str::limit($project->title, 35) }}
                                                </h5>
                                            </div>
                                            <div class="card-body d-flex flex-column">
                                                <a href="{{ route('website.display_single_project2', $project->id) }}">
                                                    <img src="{{ $project->projectimg->image }}" class="card-img-top"
                                                        alt="Project Image" style="height: 200px; object-fit: cover;">
                                                </a>
                                                <div class="post-meta mt-3">
                                                    <ul class="list-unstyled">
                                                        <li>
                                                            <i class="fas fa-calendar-alt"></i>
                                                            {{ date('d M Y', strtotime($project->created_at)) }}
                                                        </li>
                                                        <li>
                                                            @foreach ($project->sdg as $project_sdg)
                                                                <i class="fas fa-tag"></i>
                                                                {{ $project_sdg->name }}&nbsp;
                                                            @endforeach
                                                        </li>
                                                    </ul>
                                                </div>

                                                <a href="{{ route('website.display_single_project2', $project->id) }}"
                                                    class="btn btn-primary mt-auto continue-reading">Continue Reading</a>
                                            </div>
                                        </div>
                                    </div> <!-- End of card column -->
                                @endforeach
                            </div>
                        @else
                            <h3 class="text-danger text-center">No related projects found.</h3>
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
                                    <a href="{{ route('website.display_report_sdg2', $singleSdg->id) }}" class="nav-link">
                                        {{ $singleSdg->name }}
                                        @php
                                            // Set badge color based on the report count
                                            $badgeColor = 'bg-primary'; // Default color

                                            if ($singleSdg->report_count == 0) {
                                                $badgeColor = 'bg-danger';
                                            } elseif ($singleSdg->report_count >= 1) {
                                                $badgeColor = 'bg-warning';
                                            } elseif ($singleSdg->report_count >= 10) {
                                                $badgeColor = 'bg-primary';
                                            } elseif ($singleSdg->report_count >= 20) {
                                                $badgeColor = 'bg-success';
                                            }
                                        @endphp

                                        <span class="float-right badge {{ $badgeColor }}">
                                            {{ $singleSdg->report_count }}
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
