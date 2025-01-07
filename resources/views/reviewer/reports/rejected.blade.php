@extends('layouts.reviewer')
@section('title', 'Rejected Report Lists')
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Rejected Reports</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('reviewer.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Rejected Reports
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
                            <form action="{{ route('reviewer.reports.rejected') }}" method="GET" class="mb-4">
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
                                <div class="col-md-12 text-start mb-3">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('reviewer.reports.rejected') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </form>

                            <!-- Responsive Reports Table -->
                            <div class="table-responsive">
                                @if (count($reportsPaginated) > 0)
                                    <table id="reports-table" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>SDGs</th>
                                                <th>Status</th>
                                                <th>Review Status</th>
                                                <th>Project/Research</th>
                                                <th>Related Title</th>
                                                <th>Created At</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($reportsPaginated as $report)
                                                <tr>
                                                    <td>{{ $report->title }}</td>
                                                    <td>
                                                        <ul>
                                                            @foreach ($report->sdg as $sdg)
                                                                <li>{{ $sdg->name }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </td>
                                                    <td>{{ $report->is_publish == 1 ? 'Published' : 'Draft' }}</td>
                                                    <td>{{ $report->reviewStatus ? $report->reviewStatus->status : 'N/A' }}
                                                    </td>
                                                    <td>{{ ucfirst($report->related_type) }}</td>
                                                    <td>{{ $report->related_title }}</td>
                                                    <td>{{ $report->created_at->format('F j, Y, g:i A') }}</td>
                                                    <td>
                                                        <a href="{{ route('reviewer.reports.feedback_rejected', $report->id) }}"
                                                            class="btn btn-sm btn-success">View</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                            </div>

                            <!-- Custom Pagination Links -->
                            <div class="container">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center">
                                        <!-- Previous Button -->
                                        <li class="page-item {{ $reportsPaginated->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link"
                                                href="{{ $reportsPaginated->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}"
                                                tabindex="-1">Previous</a>
                                        </li>

                                        <!-- Page Number Links -->
                                        @php
                                            $currentPage = $reportsPaginated->currentPage();
                                            $lastPage = $reportsPaginated->lastPage();
                                            $start = max($currentPage - 1, 1);
                                            $end = min($start + 2, $lastPage);
                                        @endphp
                                        @for ($i = $start; $i <= $end; $i++)
                                            <li class="page-item {{ $currentPage == $i ? 'active' : '' }}">
                                                <a class="page-link"
                                                    href="{{ $reportsPaginated->url($i) . '&' . http_build_query(request()->except('page')) }}">{{ $i }}</a>
                                            </li>
                                        @endfor

                                        <!-- Next Button -->
                                        <li class="page-item {{ $reportsPaginated->hasMorePages() ? '' : 'disabled' }}">
                                            <a class="page-link"
                                                href="{{ $reportsPaginated->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}">Next</a>
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
