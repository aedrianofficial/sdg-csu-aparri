<div class="row">
    <div class="col-lg-8">
        <h5 class="text-center" data-year="title"> SDG Overview</h5>
        <div class="row">

            <!-- Total Reports Published -->
            <div class="col-lg-4 col-md-6">
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h5 class="card-title text-center">Total Reports</h5>
                    </div>
                    <div class="card-body text-center">
                        <h1>{{ $totalPublishedReports }}</h1>
                    </div>
                </div>
            </div>

            <!-- Total Projects Published -->
            <div class="col-lg-4 col-md-6">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h5 class="card-title text-center">Total Projects </h5>
                    </div>
                    <div class="card-body text-center">
                        <h1>{{ $totalPublishedProjects }}</h1>
                    </div>
                </div>
            </div>

            <!-- Total Research Published -->
            <div class="col-lg-4 col-md-6">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h5 class="card-title text-center">Total Research</h5>
                    </div>
                    <div class="card-body text-center">
                        <h1>{{ $totalPublishedResearch }}</h1>
                    </div>
                </div>
            </div>

            <!-- Most Popular SDG for Reports -->
            <div class="col-lg-4 col-md-6">
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h5 class="card-title text-center">Most Popular SDG for Reports</h5>
                    </div>
                    <div class="card-body p-0 text-center">
                        @if ($popularReportSdg)
                            <img src="{{ asset('images/sdg/E-WEB-Goal-' . sprintf('%02d', $popularReportSdg->id) . '.png') }}"
                                alt="No data available" class="img-fluid w-100 h-100"
                                style="object-fit: cover; height: 100%;">
                        @else
                            <p class="mt-3">No data available</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Most Popular SDG for Projects -->
            <div class="col-lg-4 col-md-6">
                <div class="card card-danger card-outline">
                    <div class="card-header">
                        <h5 class="card-title text-center">Most Popular SDG for Projects</h5>
                    </div>
                    <div class="card-body p-0 text-center">
                        @if ($popularProjectSdg)
                            <img src="{{ asset('images/sdg/E-WEB-Goal-' . sprintf('%02d', $popularProjectSdg->id) . '.png') }}"
                                alt="No data available" class="img-fluid w-100 h-100"
                                style="object-fit: cover; height: 100%;">
                        @else
                            <p class="mt-3">No data available</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Most Popular SDG for Research -->
            <div class="col-lg-4 col-md-6">
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h5 class="card-title text-center">Most Popular SDG for Research</h5>
                    </div>
                    <div class="card-body p-0 text-center">
                        @if ($popularResearchSdg)
                            <img src="{{ asset('images/sdg/E-WEB-Goal-' . sprintf('%02d', $popularResearchSdg->id) . '.png') }}"
                                alt="No data available" class="img-fluid w-100 h-100"
                                style="object-fit: cover; height: 100%;">
                        @else
                            <p class="mt-3">No data available</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!--line chart-->
        <div class="container" id="lineChartContainer">
            <div class="row text-center">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h4 class="text-center" style="font-weight: 600;" data-year="chart">SDG Monthly
                                Contributions Overview</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="sdgLineChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <h5 class="text-center mb-4" data-year="contributors">Top Contributor</h5>
        <div class="row">
            @if ($topContributors->isEmpty())
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card card-danger card-outline shadow-sm">
                        <div class="card-body text-center">
                            <!-- Default User Image -->
                            <div class="mb-3">
                                <img src="{{ asset('assets/website/images/user-png.png') }}" alt="Default User"
                                    class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                            <!-- Null Text -->
                            <h5 class="text-center font-weight-bold">Null</h5>
                            <!-- Default Placeholder Data -->
                            <h6 class="text-muted mb-3">No contributors available</h6>
                            <div class="text-left">
                                <p class="card-text"><i class="fas fa-project-diagram"></i>
                                    <strong>Projects:</strong> 0
                                </p>
                                <p class="card-text"><i class="fas fa-file-alt"></i> <strong>Reports:</strong> 0</p>
                                <p class="card-text"><i class="fas fa-book"></i> <strong>Research:</strong> 0</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                @foreach ($topContributors as $index => $contributor)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card card-danger card-outline shadow-sm">
                            <div class="card-body text-center">
                                <!-- User Image -->
                                <div class="mb-3">
                                    <img src="{{ $contributor->userImage ? asset($contributor->userImage->image_path) : asset('assets/website/images/user-png.png') }}"
                                        alt="{{ $contributor->first_name }} {{ $contributor->last_name }}"
                                        class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                                <h5 class="text-center font-weight-bold">{{ $contributor->first_name }}
                                    {{ $contributor->last_name }}</h5>
                                <!-- Add Icon for Top 1, Top 2, and Top 3 -->
                                <h6 class="text-muted mb-3">
                                    @if ($index == 0)
                                        <i class="fas fa-medal text-warning"></i> Top 1
                                    @elseif ($index == 1)
                                        <i class="fas fa-medal text-muted"></i> Top 2
                                    @elseif ($index == 2)
                                        <i class="fas fa-medal text-dark"></i> Top 3
                                    @else
                                        Top {{ $index + 1 }}
                                    @endif
                                </h6>
                                <div class="text-left">
                                    <p class="card-text"><i class="fas fa-project-diagram"></i>
                                        <strong>Projects:</strong> {{ $contributor->projects_count }}
                                    </p>
                                    <p class="card-text"><i class="fas fa-file-alt"></i> <strong>Reports:</strong>
                                        {{ $contributor->reports_count }}</p>
                                    <p class="card-text"><i class="fas fa-book"></i> <strong>Research:</strong>
                                        {{ $contributor->researches_count }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card card-widget card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title m-0 text-truncate" data-year="sdg">
                    <i class="fas fa-globe"></i> Sustainable Development Goals
                </h5>
            </div>
            <div class="card-footer p-0">
                <ul class="nav flex-column">
                    @foreach ($sdgs as $singleSdg)
                        <li class="nav-item">
                            <a href="{{ route('website.display_sdg_content', $singleSdg->id) }}" class="nav-link">
                                {{ $singleSdg->name }}

                                @php
                                    // Set badge color based on the total count
                                    $badgeColor = 'bg-primary'; // Default color

                                    if ($singleSdg->total_count == 0) {
                                        $badgeColor = 'bg-danger';
                                    } elseif ($singleSdg->total_count >= 1) {
                                        $badgeColor = 'bg-warning';
                                    } elseif ($singleSdg->total_count >= 10) {
                                        $badgeColor = 'bg-primary';
                                    } elseif ($singleSdg->total_count >= 20) {
                                        $badgeColor = 'bg-success';
                                    }
                                @endphp

                                <span class="float-right badge {{ $badgeColor }}">
                                    {{ $singleSdg->total_count }}
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
