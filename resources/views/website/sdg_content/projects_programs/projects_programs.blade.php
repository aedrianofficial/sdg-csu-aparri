@extends('layouts.website2')

@section('styles')
    <style>
        .post {
            transition: transform 0.2s ease-in-out;
        }

        .post:hover {
            transform: scale(1.03);
        }
    </style>
@endsection

@section('content')
    <div class="content">
        <div class="container">
            <!-- Projects Section -->
            <div class="content-header">
                <div class="container">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Projects for {{ $sdg->name }}</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('website.home') }}"><i
                                            class="fas fa-home"></i>Home</a></li>
                                <li class="breadcrumb-item active"><a href="{{ route('website.sdg_project_main') }}">All
                                        Projects</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="content">
                        <div class="container">
                            @if ($projects !== null && count($projects) > 0)
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
                                                    <a href="{{ route('website.display_single_project', $project->id) }}">
                                                        <img src="{{ $project->projectimg->image }}" class="card-img-top"
                                                            alt="" style="height: 200px; object-fit: cover;">
                                                    </a>
                                                    <div class="post-meta mt-3">
                                                        <ul class="list-unstyled">
                                                            <li>
                                                                <i class="fas fa-calendar-alt"></i>
                                                                <!-- FontAwesome icon for calendar -->
                                                                {{ date('d M Y', strtotime($project->created_at)) }}
                                                            </li>
                                                            <li>
                                                                <i class="fas fa-tags"></i>
                                                                <!-- FontAwesome icon for tags -->
                                                                @foreach ($project->sdg as $project_sdgs)
                                                                    {{ $project_sdgs->name }}&nbsp;
                                                                @endforeach
                                                            </li>
                                                        </ul>
                                                    </div>

                                                    <a href="{{ route('website.display_single_project', $project->id) }}"
                                                        class="btn btn-primary mt-auto continue-reading">Continue
                                                        Reading</a>
                                                </div>
                                            </div>
                                        </div> <!-- End of card column -->
                                    @endforeach
                                </div>

                                <!-- Pagination Links -->
                                <div class="container">
                                    @if (count($projects) > 0)
                                        <nav aria-label="Page navigation">
                                            <ul class="pagination justify-content-center">
                                                <!-- Previous Button -->
                                                <li class="page-item {{ $projects->onFirstPage() ? 'disabled' : '' }}">
                                                    <a class="page-link" href="{{ $projects->previousPageUrl() }}"
                                                        tabindex="-1">Previous</a>
                                                </li>

                                                <!-- Page Number Links -->
                                                @php
                                                    $currentPage = $projects->currentPage();
                                                    $lastPage = $projects->lastPage();
                                                    $start = max($currentPage - 1, 1);
                                                    $end = min($start + 2, $lastPage);
                                                @endphp
                                                @for ($i = $start; $i <= $end; $i++)
                                                    <li class="page-item {{ $currentPage == $i ? 'active' : '' }}">
                                                        <a class="page-link"
                                                            href="{{ $projects->url($i) }}">{{ $i }}</a>
                                                    </li>
                                                @endfor

                                                <!-- Next Button -->
                                                <li class="page-item {{ $projects->hasMorePages() ? '' : 'disabled' }}">
                                                    <a class="page-link" href="{{ $projects->nextPageUrl() }}">Next</a>
                                                </li>
                                            </ul>
                                        </nav>
                                    @else
                                        <h3 class="text-danger text-center">No projects found</h3>
                                    @endif
                                </div>
                            @else
                                <h2 class="text-center text-danger mt-5">No Projects added</h2>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- SDGs Section -->
                <div class="col-lg-4">
                    <div class="card card-widget card-danger card-outline">
                        <div class="card-header">

                            <h5 class="card-title m-0 text-truncate">
                                <i class="fas fa-globe"></i>Sustainable Development Goals
                            </h5>
                        </div>
                        <div class="card-footer p-0">
                            <ul class="nav flex-column">
                                @foreach ($sdgs as $singleSdg)
                                    <li class="nav-item">
                                        <a href="{{ route('website.display_project_sdg', $singleSdg->id) }}"
                                            class="nav-link">

                                            {{ $singleSdg->name }}

                                            @php
                                                // Set badge color based on the project count
                                                $badgeColor = 'bg-primary'; // Default color

                                                if ($singleSdg->project_count == 0) {
                                                    $badgeColor = 'bg-danger';
                                                } elseif ($singleSdg->project_count >= 1) {
                                                    $badgeColor = 'bg-warning';
                                                } elseif ($singleSdg->project_count >= 10) {
                                                    $badgeColor = 'bg-primary';
                                                } elseif ($singleSdg->project_count >= 20) {
                                                    $badgeColor = 'bg-success';
                                                }
                                            @endphp

                                            <span
                                                class="float-right badge {{ $badgeColor }}">{{ $singleSdg->project_count }}</span>
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
