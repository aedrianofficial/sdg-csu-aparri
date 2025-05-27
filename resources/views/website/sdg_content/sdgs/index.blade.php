@extends('layouts.website2')

@section('content')
    <div class="content">
        <div class="container">
            <div class="text-center mb-4">
                <p class="lead text-muted">{{ $sdg->description }}</p>

                <img src="{{ asset('assets/website/images/SDG/sdg-' . $sdg->id . '.avif') }}" alt="{{ $sdg->name }}"
                    class="sdg-image img-fluid" style="width: 100%; height: 50vh;">
            </div>

            <div class="content-filter text-center mb-4">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-dark active" id="show-all-btn">All</button>
                    <button type="button" class="btn btn-dark" id="show-projects-btn">Projects</button>
                    <button type="button" class="btn btn-dark" id="show-research-btn">Research</button>
                </div>
            </div>

            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <div class="row" id="content-container">
                        <!-- Projects Section -->
                        @foreach ($projects as $project)
                            <div class="col-md-6 mb-4 content-item project-item">
                                <div class="card card-primary card-outline h-100 d-flex flex-column post">
                                    <div class="card-header">
                                        <h5 class="card-title m-0 text-truncate" title="{{ $project->title }}">
                                            <i class="fas fa-project-diagram"></i>
                                            {{ Str::limit($project->title, 35) }}
                                        </h5>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <a href="{{ route('website.display_single_project', $project->id) }}">
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

                                        <!-- Gender Impact Summary -->
                                        @include('website.sdg_content.partials.gender_impact_summary', ['genderImpact' => $project->genderImpact])

                                        <a href="{{ route('website.display_single_project', $project->id) }}"
                                            class="btn btn-primary mt-auto continue-reading">Continue
                                            Reading</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Research Section -->
                        @foreach ($researches as $singleResearch)
                            <div class="col-md-6 mb-4 content-item research-item">
                                <div class="card card-secondary card-outline h-100 d-flex flex-column post">
                                    <div class="card-header">
                                        <h5 class="card-title m-0 text-truncate" title="{{ $singleResearch->title }}">
                                            <i class="fas fa-book"></i>
                                            {{ Str::limit($singleResearch->title, 35) }}
                                        </h5>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <div class="post-meta mt-3">
                                            <ul class="list-unstyled">
                                                <li>
                                                    <i class="fas fa-calendar-alt"></i>
                                                    {{ date('d M Y', strtotime($singleResearch->created_at)) }}
                                                </li>
                                                <li>
                                                    <i class="fas fa-tags"></i>
                                                    @foreach ($singleResearch->sdg as $research_sdg)
                                                        {{ $research_sdg->name }}&nbsp;
                                                    @endforeach
                                                </li>
                                                <li>
                                                    <i class="fas fa-folder"></i>
                                                    {{ $singleResearch->researchCategory->name ?? 'No Category' }}
                                                </li>
                                            </ul>
                                        </div>

                                        <!-- Gender Impact Summary -->
                                        @include('website.sdg_content.partials.gender_impact_summary', ['genderImpact' => $singleResearch->genderImpact])

                                        <a href="{{ route('website.display_single_research', $singleResearch->id) }}"
                                            class="btn btn-secondary mt-auto continue-reading">
                                            Continue Reading
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Show a message if no content is available -->
                    <div id="no-content-message" class="text-center d-none">
                        <p class="lead">No content available for this filter.</p>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- SDGs Section -->
                    <div class="card card-widget card-danger card-outline">
                        <div class="card-header">
                            <h5 class="card-title m-0 text-truncate"><i class="fas fa-bullseye"></i> Sustainable Development
                                Goals</h5>
                        </div>
                        <div class="card-footer p-0">
                            <ul class="nav flex-column">
                                @foreach ($sdgPublished as $singleSdg)
                                    <li class="nav-item">
                                        <a href="{{ route('website.sdg.show', $singleSdg->id) }}"
                                            class="nav-link">

                                            @php
                                                // Set badge color based on research count for categories
                                                $badgeColor = 'bg-primary';
                                                if ($singleSdg->project_research_count == 0) {
                                                    $badgeColor = 'bg-danger';
                                                } elseif (
                                                    $singleSdg->project_research_count >= 1 &&
                                                    $singleSdg->project_research_count < 10
                                                ) {
                                                    $badgeColor = 'bg-warning';
                                                } elseif ($singleSdg->project_research_count >= 20) {
                                                    $badgeColor = 'bg-success';
                                                }
                                            @endphp

                                            {{ $singleSdg->name }}
                                            <span class="float-right badge {{ $badgeColor }}">
                                                {{ $singleSdg->project_research_count }}
                                            </span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Research Categories Section - Only visible when viewing research -->
                    <div class="card card-widget card-info card-outline mt-4" id="research-categories-section">
                        <div class="card-header">
                            <h5 class="card-title m-0 text-truncate"><i class="fas fa-list-alt"></i> Research Categories
                            </h5>
                        </div>
                        <div class="card-footer p-0">
                            <ul class="nav flex-column">
                                @foreach ($researchCategories as $category)
                                    <li class="nav-item">
                                        <a href="{{ route('website.display_research_category', $category->id) }}"
                                            class="nav-link">

                                            @php
                                                // Set badge color based on research count for categories
                                                $badgeColor = 'bg-primary';
                                                if ($category->research_count == 0) {
                                                    $badgeColor = 'bg-danger';
                                                } elseif (
                                                    $category->research_count >= 1 &&
                                                    $category->research_count < 10
                                                ) {
                                                    $badgeColor = 'bg-warning';
                                                } elseif ($category->research_count >= 20) {
                                                    $badgeColor = 'bg-success';
                                                }
                                            @endphp

                                            {{ $category->name }}
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
    </div>
@endsection
<!-- JavaScript to handle filtering -->
@section('scripts')
 
    <script>
        $(document).ready(function() {
            // Filter buttons event handlers
            $('#show-all-btn').click(function() {
                $(this).addClass('active').siblings().removeClass('active');
                $('.content-item').show();
                $('#research-categories-section').show(); // Show research categories when showing all
                checkNoContent();
            });

            $('#show-projects-btn').click(function() {
                $(this).addClass('active').siblings().removeClass('active');
                $('.research-item').hide();
                $('.project-item').show();
                $('#research-categories-section')
                    .hide(); // Hide research categories when showing only projects
                checkNoContent();
            });

            $('#show-research-btn').click(function() {
                $(this).addClass('active').siblings().removeClass('active');
                $('.project-item').hide();
                $('.research-item').show();
                $('#research-categories-section').show(); // Show research categories when showing research
                checkNoContent();
            });

            // Function to check if there's content to display
            function checkNoContent() {
                const visibleItems = $('.content-item:visible').length;
                if (visibleItems === 0) {
                    $('#no-content-message').removeClass('d-none');
                } else {
                    $('#no-content-message').addClass('d-none');
                }
            }

            // Initial check
            checkNoContent();
        });
    </script>
@endsection
