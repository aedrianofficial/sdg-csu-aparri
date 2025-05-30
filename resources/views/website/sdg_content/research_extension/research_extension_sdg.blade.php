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
            <!-- Research Section -->
            <div class="content-header">
                <div class="container">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Research for {{ $sdg->name }}</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('website.home') }}"><i
                                            class="fas fa-home"></i> Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('website.sdg_research_main') }}"></a>All
                                    Research</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <div class="content">
                        <div class="container">
                            @if ($research !== null && count($research) > 0)
                                <div class="row">
                                    @foreach ($research as $item)
                                        <div class="col-lg-6 mb-4"> <!-- Column for each card -->
                                            <div class="card card-secondary card-outline h-100 d-flex flex-column post">
                                                <div class="card-header">
                                                    <h5 class="card-title m-0 text-truncate" title="{{ $item->title }}">
                                                        <i class="fas fa-book"></i>
                                                        {{ Str::limit($item->title, 35) }}
                                                    </h5>
                                                </div>
                                                <div class="card-body d-flex flex-column">

                                                    <div class="post-meta mt-3">
                                                        <ul class="list-unstyled">
                                                            <li>
                                                                <i class="fas fa-calendar-alt"></i>
                                                                {{ date('d M Y', strtotime($item->created_at)) }}
                                                            </li>
                                                            <li>
                                                                @foreach ($item->sdg as $item_sdgs)
                                                                    <i class="fas fa-tags"></i>
                                                                    {{ $item_sdgs->name }}&nbsp;
                                                                @endforeach
                                                            </li>
                                                            <li>
                                                                <i class="fas fa-folder"></i>
                                                                {{ $item->researchcategory->name ?? 'No Category' }}
                                                            </li>
                                                        </ul>
                                                    </div>

                                                    <!-- Gender Impact Summary -->
                                                    @include('website.sdg_content.partials.gender_impact_summary', ['genderImpact' => $item->genderImpact])

                                                    <a href="{{ route('website.display_single_research', $item->id) }}"
                                                        class="btn btn-secondary mt-auto continue-reading">
                                                        <i class="fas fa-arrow-right"></i> Continue Reading
                                                    </a>
                                                </div>
                                            </div>
                                        </div> <!-- End of card column -->
                                    @endforeach
                                </div>

                                <!-- Pagination Links -->
                                <div class="container">
                                    @if (count($research) > 0)
                                        <nav aria-label="Page navigation">
                                            <ul class="pagination justify-content-center">
                                                <!-- Previous Button -->
                                                <li class="page-item {{ $research->onFirstPage() ? 'disabled' : '' }}">
                                                    <a class="page-link" href="{{ $research->previousPageUrl() }}"
                                                        tabindex="-1"><i class="fas fa-chevron-left"></i> Previous</a>
                                                </li>

                                                <!-- Page Number Links -->
                                                @php
                                                    $currentPage = $research->currentPage();
                                                    $lastPage = $research->lastPage();
                                                    $start = max($currentPage - 1, 1);
                                                    $end = min($start + 2, $lastPage);
                                                @endphp
                                                @for ($i = $start; $i <= $end; $i++)
                                                    <li class="page-item {{ $currentPage == $i ? 'active' : '' }}">
                                                        <a class="page-link"
                                                            href="{{ $research->url($i) }}">{{ $i }}</a>
                                                    </li>
                                                @endfor

                                                <!-- Next Button -->
                                                <li class="page-item {{ $research->hasMorePages() ? '' : 'disabled' }}">
                                                    <a class="page-link" href="{{ $research->nextPageUrl() }}">Next <i
                                                            class="fas fa-chevron-right"></i></a>
                                                </li>
                                            </ul>
                                        </nav>
                                    @else
                                        <h3 class="text-danger text-center">No research found</h3>
                                    @endif
                                </div>
                            @else
                                <h2 class="text-center text-danger mt-5">No Research added</h2>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- SDGs Section -->
                <div class="col-lg-4">
                    <div class="card card-widget card-danger card-outline">
                        <div class="card-header">
                            <h5 class="card-title m-0 text-truncate">
                                <i class="fas fa-globe"></i> Sustainable Development Goals
                            </h5>
                        </div>
                        <div class="card-footer p-0">
                            <ul class="nav flex-column">
                                @foreach ($sdgs as $singleSdg)
                                    <li class="nav-item">
                                        <a href="{{ route('website.display_research_sdg', $singleSdg->id) }}"
                                            class="nav-link">
                                            {{ $singleSdg->name }}

                                            @php
                                                // Set badge color based on the research count
                                                $badgeColor = 'bg-primary'; // Default color

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

                                            <span
                                                class="float-right badge {{ $badgeColor }}">{{ $singleSdg->research_count }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Research Categories Section -->
                    <div class="card card-widget card-info card-outline mt-3">
                        <div class="card-header">
                            <h5 class="card-title m-0 text-truncate">
                                <i class="fas fa-list-alt"></i> Research Categories
                            </h5>
                        </div>
                        <div class="card-footer p-0">
                            <ul class="nav flex-column">
                                @foreach ($researchCategories as $category)
                                    <li class="nav-item">
                                        <a href="{{ route('website.display_research_category', $category->id) }}"
                                            class="nav-link">
                                            {{ $category->name }}
                                            @php
                                                // Set badge color based on the research count
                                                $badgeColor = 'bg-primary'; // Default color

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
                                            <span
                                                class="float-right badge {{ $badgeColor }}">{{ $category->research_count }}</span>
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
