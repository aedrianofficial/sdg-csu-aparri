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
                        <li class="breadcrumb-item"><a href="{{ route('website.home2') }}"><i class="fas fa-home"></i> Home</a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('website.sdg_research_main2') }}"></a>All
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
                                <span>{!! $research->description !!}</span>
                            </div>

                            <!--File-->
                            <div class="research-detail-item mb-2">
                                <strong><i class="fas fa-file"></i> Files:</strong>
                                <ul>
                                    @if ($research->researchfiles->isEmpty())
                                        <li class="text-muted">No files available for this research.</li>
                                    @else
                                        @foreach ($research->researchfiles as $file)
                                            <li>
                                                <i class="fas fa-download"></i>
                                                <a href="{{ route('research.file.download', $file->id) }}" target="_blank"
                                                    rel="noopener noreferrer">
                                                    Download Abstract: {{ $file->original_filename ?? $research->title }}
                                                </a>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
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
                                    <a href="{{ route('website.display_research_sdg2', $singleSdg->id) }}"
                                        class="nav-link">
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
