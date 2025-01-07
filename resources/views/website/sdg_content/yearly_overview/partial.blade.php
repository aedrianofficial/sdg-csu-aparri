<div class="row">
    <div class="col-lg-8">
        <h5 class="text-center" data-year="title"> SDG Overview</h5>
        <div class="row">

            <!-- Total Status Reports Published -->
            <div class="col-lg-6 col-md-6">
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h5 class="card-title text-center">Total Status Reports</h5>
                    </div>
                    <div class="card-body text-center">
                        <h1>{{ $totalPublishedStatusReports }}</h1>
                    </div>
                </div>
            </div>

            <!-- Total Terminal Reports Published -->
            <div class="col-lg-6 col-md-6">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h5 class="card-title text-center">Total Terminal Reports</h5>
                    </div>
                    <div class="card-body text-center">
                        <h1>{{ $totalPublishedTerminalReports }}</h1>
                    </div>
                </div>
            </div>

            <!-- Total Projects Published -->
            <div class="col-lg-6 col-md-6">
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
            <div class="col-lg-6 col-md-6">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h5 class="card-title text-center">Total Research</h5>
                    </div>
                    <div class="card-body text-center">
                        <h1>{{ $totalPublishedResearch }}</h1>
                    </div>
                </div>
            </div>

            <!-- Most Popular SDG for Projects -->
            <div class="col-lg-6 col-md-6">
                <div class="card card-danger card-outline">
                    <div class="card-header">
                        <h5 class="card-title text-center">Most Popular SDG for Projects</h5>
                    </div>
                    <div class="card-body p-0 text-center">
                        @if ($totalPublishedProjects == 0)
                            <img src="{{ asset('images/sdg/exclamation.png') }}" alt="No data available"
                                class="img-fluid w-100 h-100" style="object-fit: cover; height: 100%;">
                        @elseif ($popularProjectSdg)
                            @php
                                $extensions = ['png', 'jpg', 'jpeg', 'gif', 'webp']; // Allowed extensions
                                $imagePath = null;

                                foreach ($extensions as $extension) {
                                    $path =
                                        'images/sdg/E-WEB-Goal-' .
                                        sprintf('%02d', $popularProjectSdg->id) .
                                        '.' .
                                        $extension;
                                    if (file_exists(public_path($path))) {
                                        $imagePath = asset($path);
                                        break;
                                    }
                                }
                            @endphp

                            <img src="{{ $imagePath ?? asset('images/default.png') }}" alt="No data available"
                                class="img-fluid w-100 h-100" style="object-fit: cover; height: 100%;">
                        @else
                            <p class="mt-3">No data available</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Most Popular SDG for Research -->
            <div class="col-lg-6 col-md-6">
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h5 class="card-title text-center">Most Popular SDG for Research</h5>
                    </div>
                    <div class="card-body p-0 text-center">
                        @if ($totalPublishedResearch == 0)
                            <img src="{{ asset('images/sdg/exclamation.png') }}" alt="No data available"
                                class="img-fluid w-100 h-100" style="object-fit: cover; height: 100%;">
                        @elseif ($popularResearchSdg)
                            @php
                                $extensions = ['png', 'jpg', 'jpeg', 'gif', 'webp']; // Allowed extensions
                                $imagePath = null;

                                foreach ($extensions as $extension) {
                                    $path =
                                        'images/sdg/E-WEB-Goal-' .
                                        sprintf('%02d', $popularResearchSdg->id) .
                                        '.' .
                                        $extension;
                                    if (file_exists(public_path($path))) {
                                        $imagePath = asset($path);
                                        break;
                                    }
                                }
                            @endphp

                            <img src="{{ $imagePath ?? asset('images/default.png') }}" alt="No data available"
                                class="img-fluid w-100 h-100" style="object-fit: cover; height: 100%;">
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
        <h5 class="text-center mb-4">Top Contributors</h5>
        <div class="row">
            <!-- Top Contributor for Projects -->
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="card card-danger card-outline shadow-sm">
                    <div class="card-body text-center">
                        @if ($topContributorForProjects)
                            <div class="mb-3">
                                <img src="{{ $topContributorForProjects->userImage->image_path ?? asset('assets/website/images/user-png.png') }}"
                                    alt="{{ $topContributorForProjects->first_name ?? 'No Contributor' }}"
                                    class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                            <h5 class="font-weight-bold">
                                {{ $topContributorForProjects->first_name ?? 'No Contributor' }}
                                {{ $topContributorForProjects->last_name ?? '' }}
                            </h5>
                            <i class="fas fa-medal text-warning"></i>
                            <h6 class="text-muted">Top Contributor for Projects</h6>
                            <p class="card-text"><i class="fas fa-project-diagram"></i>
                                <strong>Projects:</strong> {{ $topContributorForProjects->projects_count ?? 0 }}
                            </p>
                        @else
                            <p class="text-muted">No contributors available for Projects.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Top Contributor for Research -->
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="card card-danger card-outline shadow-sm">
                    <div class="card-body text-center">
                        @if ($topContributorForResearch)
                            <div class="mb-3">
                                <img src="{{ $topContributorForResearch->userImage->image_path ?? asset('assets/website/images/user-png.png') }}"
                                    alt="{{ $topContributorForResearch->first_name ?? 'No Contributor' }}"
                                    class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                            <h5 class="font-weight-bold">
                                {{ $topContributorForResearch->first_name ?? 'No Contributor' }}
                                {{ $topContributorForResearch->last_name ?? '' }}
                            </h5>
                            <i class="fas fa-medal text-warning"></i>
                            <h6 class="text-muted">Top Contributor for Research</h6>
                            <p class="card-text"><i class="fas fa-book"></i>
                                <strong>Research:</strong> {{ $topContributorForResearch->researches_count ?? 0 }}
                            </p>
                        @else
                            <p class="text-muted">No contributors available for Research.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Top Contributor for Status Reports -->
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="card card-danger card-outline shadow-sm">
                    <div class="card-body text-center">
                        @if ($topContributorForStatusReports)
                            <div class=" mb-3">
                                <img src="{{ $topContributorForStatusReports->userImage->image_path ?? asset('assets/website/images/user-png.png') }}"
                                    alt="{{ $topContributorForStatusReports->first_name ?? 'No Contributor' }}"
                                    class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                            <h5 class="font-weight-bold">
                                {{ $topContributorForStatusReports->first_name ?? 'No Contributor' }}
                                {{ $topContributorForStatusReports->last_name ?? '' }}
                            </h5>
                            <i class="fas fa-medal text-warning"></i>
                            <h6 class="text-muted">Top Contributor for Status Reports</h6>
                            <p class="card-text"><i class="fas fa-file-alt"></i>
                                <strong>Status Reports:</strong>
                                {{ $topContributorForStatusReports->status_reports_count ?? 0 }}
                            </p>
                        @else
                            <p class="text-muted">No contributors available for Status Reports.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Top Contributor for Terminal Reports -->
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="card card-danger card-outline shadow-sm">
                    <div class="card-body text-center">
                        @if ($topContributorForTerminalReports)
                            <div class="mb-3">
                                <img src="{{ $topContributorForTerminalReports->userImage->image_path ?? asset('assets/website/images/user-png.png') }}"
                                    alt="{{ $topContributorForTerminalReports->first_name ?? 'No Contributor' }}"
                                    class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                            <h5 class="font-weight-bold">
                                {{ $topContributorForTerminalReports->first_name ?? 'No Contributor' }}
                                {{ $topContributorForTerminalReports->last_name ?? '' }}
                            </h5>
                            <i class="fas fa-medal text-warning"></i>
                            <h6 class="text-muted">Top Contributor for Terminal Reports</h6>
                            <p class="card-text"><i class="fas fa-file-alt"></i>
                                <strong>Terminal Reports:</strong>
                                {{ $topContributorForTerminalReports->terminal_reports_count ?? 0 }}
                            </p>
                        @else
                            <p class="text-muted">No contributors available for Terminal Reports.</p>
                        @endif
                    </div>
                </div>
            </div>
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
