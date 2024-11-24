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
                            <h1 class="m-0">All Research</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('website.home2') }}"><i
                                            class="fas fa-home"></i> Home</a></li>
                                <li class="breadcrumb-item active">All Research</li>
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
                                    @foreach ($research as $singleResearch)
                                        <div class="col-lg-6 mb-4">
                                            <div class="card card-secondary card-outline h-100 d-flex flex-column post">
                                                <div class="card-header">
                                                    <h5 class="card-title m-0 text-truncate"
                                                        title="{{ $singleResearch->title }}">
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
                                                                {{ $singleResearch->researchcategory->name ?? 'No Category' }}
                                                            </li>
                                                        </ul>
                                                    </div>

                                                    <a href="{{ route('website.display_single_research2', $singleResearch->id) }}"
                                                        class="btn btn-secondary mt-auto continue-reading">
                                                        {{-- <i class="fas fa-arrow-right"></i> --}} Continue Reading
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Pagination Links -->
                                <div class="container">
                                    @if (count($research) > 0)
                                        <nav aria-label="Page navigation">
                                            <ul class="pagination justify-content-center">
                                                <li class="page-item {{ $research->onFirstPage() ? 'disabled' : '' }}">
                                                    <a class="page-link" href="{{ $research->previousPageUrl() }}"
                                                        tabindex="-1">
                                                        <i class="fas fa-chevron-left"></i> Previous
                                                    </a>
                                                </li>
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
                                                <li class="page-item {{ $research->hasMorePages() ? '' : 'disabled' }}">
                                                    <a class="page-link" href="{{ $research->nextPageUrl() }}">
                                                        Next <i class="fas fa-chevron-right"></i>
                                                    </a>
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

                <!-- SDGs and Research Categories Section -->
                <div class="col-lg-4">
                    <!-- SDGs Section -->
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

                                            @php
                                                // Set badge color based on research count for categories
                                                $badgeColor = 'bg-primary';
                                                if ($singleSdg->research_count == 0) {
                                                    $badgeColor = 'bg-danger';
                                                } elseif (
                                                    $singleSdg->research_count >= 1 &&
                                                    $singleSdg->research_count < 10
                                                ) {
                                                    $badgeColor = 'bg-warning';
                                                } elseif ($singleSdg->research_count >= 20) {
                                                    $badgeColor = 'bg-success';
                                                }
                                            @endphp

                                            {{ $singleSdg->name }}
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
                    <div class="card card-widget card-info card-outline mt-4">
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
