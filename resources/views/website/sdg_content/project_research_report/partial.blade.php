<div class="col-lg-8">

    <!-- Projects Section -->
    <div class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Projects</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('website.sdg_project_main2') }}">Show
                                All Projects</a></li>
                        <li class="breadcrumb-item active">Home</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container">
            @if ($projects !== null && count($projects) > 0)
                <div class="row">
                    @foreach ($projects as $project)
                        <div class="col-lg-6 mb-4">
                            <div class="card card-primary card-outline h-100 d-flex flex-column post">
                                <div class="card-header">
                                    <h5 class="card-title m-0 text-truncate" title="{{ $project->title }}">
                                        {{ Str::limit($project->title, 30) }}
                                    </h5>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <a href="{{ route('website.display_single_project2', $project->id) }}">
                                        <img src="{{ $project->projectimg->image }}" class="card-img-top"
                                            alt="" style="height: 200px; object-fit: cover;">
                                    </a>
                                    <div class="post-meta mt-3">
                                        <ul class="list-unstyled">
                                            <li><i class="ion-calendar"></i>
                                                {{ date('d M Y', strtotime($project->created_at)) }}</li>
                                            <li>
                                                @foreach ($project->sdg as $project_sdgs)
                                                    <i class="ion-pricetags">{{ $project_sdgs->name }}&nbsp;</i>
                                                @endforeach
                                            </li>
                                        </ul>
                                    </div>
                                    <a href="{{ route('website.display_single_project2', $project->id) }}"
                                        class="btn btn-primary mt-auto continue-reading">Continue
                                        Reading</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <h2 class="text-center text-danger mt-5">No Projects added</h2>
            @endif
            <div class="container">
                <!-- Custom Pagination Links -->
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center">
                        <!-- Previous Button -->
                        <li class="page-item {{ $projects->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $projects->appends(request()->query())->previousPageUrl() }}"
                                tabindex="-1">
                                Previous
                            </a>
                        </li>

                        <!-- Page Number Links -->
                        @php
                            $currentPage = $projects->currentPage(); // Current page number
                            $lastPage = $projects->lastPage(); // Last page number
                            $start = max($currentPage - 1, 1); // Calculate start of the visible page items
                            $end = min($start + 2, $lastPage); // Calculate end of the visible page items
                        @endphp

                        @for ($i = $start; $i <= $end; $i++)
                            <li class="page-item {{ $currentPage == $i ? 'active' : '' }}">
                                <a class="page-link"
                                    href="{{ $projects->appends(request()->query())->url($i) }}">{{ $i }}</a>
                            </li>
                        @endfor

                        <!-- Next Button -->
                        <li class="page-item {{ $projects->hasMorePages() ? '' : 'disabled' }}">
                            <a class="page-link" href="{{ $projects->appends(request()->query())->nextPageUrl() }}">
                                Next
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Research Section -->
    <div class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Research</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('website.sdg_research_main2') }}">Show All
                                Research</a></li>
                        <li class="breadcrumb-item active">Home</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container">
            @if ($research !== null && count($research) > 0)
                <div class="row">
                    @foreach ($research as $singleResearch)
                        <div class="col-lg-6 mb-4">
                            <div class="card card-secondary card-outline h-100 d-flex flex-column post">
                                <div class="card-header">
                                    <h5 class="card-title m-0 text-truncate" title="{{ $singleResearch->title }}">
                                        {{ Str::limit($singleResearch->title, 30) }}
                                    </h5>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <div class="post-meta mt-3">
                                        <ul class="list-unstyled">
                                            <li><i class="ion-calendar"></i>
                                                {{ date('d M Y', strtotime($singleResearch->created_at)) }}
                                            </li>
                                            <li>
                                                @foreach ($singleResearch->sdg as $singleResearch_sdgs)
                                                    <i
                                                        class="ion-pricetags">{{ $singleResearch_sdgs->name }}&nbsp;</i>
                                                @endforeach
                                            </li>
                                            <li>
                                                <i class="ion-pricetags"></i>
                                                {{ $singleResearch->researchcategory->name ?? 'No Category' }}
                                            </li>
                                        </ul>
                                    </div>
                                    <a href="{{ route('website.display_single_research2', $singleResearch->id) }}"
                                        class="btn btn-secondary mt-auto continue-reading">Continue
                                        Reading</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <h2 class="text-center text-danger mt-5">No Research added</h2>
            @endif
            <div class="container">
                <!-- Custom Pagination Links -->
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center">
                        <!-- Previous Button -->
                        <li class="page-item {{ $research->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $research->appends(request()->query())->previousPageUrl() }}" tabindex="-1">
                                Previous
                            </a>
                        </li>
            
                        <!-- Page Number Links -->
                        @php
                            $currentPage = $research->currentPage(); // Current page number
                            $lastPage = $research->lastPage(); // Last page number
                            $start = max($currentPage - 1, 1); // Calculate start of the visible page items
                            $end = min($start + 2, $lastPage); // Calculate end of the visible page items
                        @endphp
            
                        @for ($i = $start; $i <= $end; $i++)
                            <li class="page-item {{ $currentPage == $i ? 'active' : '' }}">
                                <a class="page-link" href="{{ $research->appends(request()->query())->url($i) }}">{{ $i }}</a>
                            </li>
                        @endfor
            
                        <!-- Next Button -->
                        <li class="page-item {{ $research->hasMorePages() ? '' : 'disabled' }}">
                            <a class="page-link" href="{{ $research->appends(request()->query())->nextPageUrl() }}">
                                Next
                            </a>
                        </li>
                    </ul>
                </nav>
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
