@extends('layouts.reviewer')
@section('title', 'Reviewed Report Lists')
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Reviewed Reports </h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Reviewed Reports
                        </li>
                    </ol>
                </div>
            </div> <!--end::Row-->
        </div> <!--end::Container-->
    </div> <!--end::App Content Header--> <!--begin::App Content-->
    <div class="app-content"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row"> <!--begin::Col-->
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <!-- Search and Filter Form -->
                            <form action="{{ route('reviewer.reports.reviewed_list') }}" method="GET" class="mb-4">
                                <div class="row">
                                    <!-- Title Filter -->
                                    <div class="col-md-3 mb-3">
                                        <div class="form-group">
                                            <label for="title" class="form-label">Search:</label>
                                            <input id="search" type="text" name="title" class="form-control"
                                                value="{{ request('title') }}" placeholder="Enter Title">
                                        </div>
                                    </div>
                                </div>

                                <h6>Filter by:</h6>
                                <div class="row">
                                    <!-- Related Type Filter -->
                                    <div class="col-md-3 mb-3">
                                        <div class="form-group">
                                            <label for="related_type" class="form-label">Related Type
                                                (Project/Research):</label>
                                            <select name="related_type" class="form-select">
                                                <option value="">Select Related Type</option>
                                                <option value="project"
                                                    {{ request()->related_type == 'project' ? 'selected' : '' }}>
                                                    Project
                                                </option>
                                                <option value="research"
                                                    {{ request()->related_type == 'research' ? 'selected' : '' }}>
                                                    Research
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Review Status Filter -->
                                    <div class="col-md-3 mb-3">
                                        <div class="form-group">
                                            <label for="review_status" class="form-label">Review Status:</label>
                                            <select id="review_status" name="review_status" class="form-select">
                                                <option value="">All</option>
                                                @foreach ($reviewStatuses as $status)
                                                    <option value="{{ $status->id }}"
                                                        {{ request('review_status') == $status->id ? 'selected' : '' }}>
                                                        {{ $status->status }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- SDG Filter -->
                                    <div class="col-md-3 mb-3">
                                        <div class="form-group">
                                            <label for="sdg" class="form-label">Select SDG:</label>
                                            <select id="select2-multiple" name="sdg[]" class="form-select" multiple>
                                                @foreach ($sdgs as $sdg)
                                                    <option value="{{ $sdg->id }}"
                                                        {{ in_array($sdg->id, request('sdg', [])) ? 'selected' : '' }}>
                                                        {{ $sdg->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Filter and Reset Buttons -->
                                <div class="col-md-12 text-start">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('reviewer.reports.reviewed_list') }}"
                                        class="btn btn-secondary">Reset</a>
                                </div>
                            </form>

                            <!-- Responsive Reports Table -->
                            <div class="table-responsive">
                                @if (count($reports) > 0)
                                    <table id="reports-table" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>SDGs</th>
                                                <th>Status</th>
                                                <th>Review Status</th>
                                                <th>Project/Research</th>
                                                <th>Item</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($reports as $report)
                                                <tr>
                                                    <td>{{ Str::limit($report->title, 15, '...') }}</td>
                                                    <td>
                                                        <ul>
                                                            @foreach ($report->sdg as $sdg)
                                                                <li>{{ Str::limit($sdg->name, 15, '...') }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </td>
                                                    <td>{{ $report->is_publish == 1 ? 'Published' : 'Draft' }}</td>
                                                    <td>{{ $report->reviewStatus ? Str::limit($report->reviewStatus->status, 15, '...') : 'N/A' }}
                                                    </td>
                                                    <td>{{ ucfirst($report->related_type) }}</td>
                                                    <td>{{ Str::limit($report->related_title, 15, '...') }}</td>
                                                    <td>
                                                        <a href="{{ route('reviewer.reports.show_reviewed', $report->id) }}"
                                                            class="btn btn-sm btn-success">View</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                            </div>

                            <!-- Custom Pagination Links -->
                            <div class="container">
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination justify-content-center">
                                        <!-- Previous Button -->
                                        <li class="page-item {{ $reports->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $reports->previousPageUrl() }}"
                                                tabindex="-1">Previous</a>
                                        </li>

                                        <!-- Page Number Links -->
                                        @php
                                            $currentPage = $reports->currentPage();
                                            $lastPage = $reports->lastPage();
                                            $start = max($currentPage - 1, 1);
                                            $end = min($start + 2, $lastPage);
                                        @endphp

                                        @for ($i = $start; $i <= $end; $i++)
                                            <li class="page-item {{ $currentPage == $i ? 'active' : '' }}">
                                                <a class="page-link"
                                                    href="{{ $reports->url($i) }}">{{ $i }}</a>
                                            </li>
                                        @endfor

                                        <!-- Next Button -->
                                        <li class="page-item {{ $reports->hasMorePages() ? '' : 'disabled' }}">
                                            <a class="page-link" href="{{ $reports->nextPageUrl() }}">Next</a>
                                        </li>
                                    </ul>
                                </nav>
                            @else
                                <h3 class="text-danger text-center">No reports found</h3>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div> <!--end::Container-->
    </div>
    <!--end::App Content-->
@endsection

@section('scripts')

    <script>
        $(document).ready(function() {

            $('#select2-multiple').select2({
                width: '100%',
                placeholder: 'Select SDGs',
                allowClear: true
            });
        });
    </script>
@endsection
