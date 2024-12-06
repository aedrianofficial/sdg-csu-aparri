@extends('layouts.reviewer')
@section('title', 'Under Review Report Lists')
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Under Review Reports</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Under Review Reports</li>
                    </ol>
                </div>
            </div> <!--end::Row-->
        </div> <!--end::Container-->
    </div> <!--end::App Content Header-->

    <div class="app-content"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row"> <!--begin::Col-->
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('reviewer.reports.under_review') }}" method="GET" class="mb-4">
                                <div class="row">
                                    <!-- Title Filter -->
                                    <div class="col-md-3 mb-3">
                                        <label for="title" class="form-label">Search:</label>
                                        <input id="search" type="text" name="title" class="form-control"
                                            value="{{ request('title') }}" placeholder="Enter Title">
                                    </div>
                                </div>
                                <h6>Filter by:</h6>

                                <div class="row"> <!-- Start a new row for the next filter -->
                                    <!-- Related Type Filter -->
                                    <div class="col-md-3 mb-3">
                                        <label for="related_type" class="form-label">Related Type
                                            (Project/Research):</label>
                                        <select name="related_type" class="form-select select2">
                                            <option value="">Select Related Type</option>
                                            <option value="project"
                                                {{ request()->related_type == 'project' ? 'selected' : '' }}>Project
                                            </option>
                                            <option value="research"
                                                {{ request()->related_type == 'research' ? 'selected' : '' }}>Research
                                            </option>
                                        </select>
                                    </div>

                                    <!-- SDG Filter -->
                                    <div class="col-md-3 mb-3">
                                        <label for="sdg" class="form-label">Select SDG:</label>
                                        <select id="sdg" name="sdg[]" class="form-select select2-multiple" multiple>
                                            @foreach ($sdgs as $sdg)
                                                <option value="{{ $sdg->id }}"
                                                    {{ in_array($sdg->id, request('sdg', [])) ? 'selected' : '' }}>
                                                    {{ $sdg->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Filter and Reset Buttons -->
                                <div class="col-md-12 text-left mb-3">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('reviewer.reports.under_review') }}"
                                        class="btn btn-secondary">Reset</a>
                                </div>
                            </form>

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
                                                <th>Related Title</th>
                                                <th>Created At</th>
                                                <th>Action</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($reports as $report)
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
                                                    <td>
                                                        {{ $report->reviewStatus ? $report->reviewStatus->status : 'N/A' }}
                                                    </td>
                                                    <td>{{ ucfirst($report->related_type) }}</td>
                                                    <td>{{ $report->related_title }}</td>
                                                    <td>{{ $report->created_at->format('F j, Y, g:i A') }}</td>
                                                    <td>
                                                        <a href="{{ route('reviewer.reports.show', $report->id) }}"
                                                            class="btn btn-sm btn-success">View</a>
                                                    </td>
                                                    <td> <!-- Button for 'Need Changes' -->
                                                        <button type="button" class="btn btn-sm btn-secondary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#needChangesModal{{ $report->id }}">
                                                            Changes</button>
                                                    </td>
                                                    <td>
                                                        <!-- Button for 'Reject' -->
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#rejectModal{{ $report->id }}">Reject</button>
                                                    </td>
                                                    <td>
                                                          <!-- Forward to Approver Button -->
                                                          <button type="button" class="btn btn-sm btn-primary"
                                                          data-bs-toggle="modal"
                                                          data-bs-target="#confirmReviewModal{{ $report->id }}">Reviewed</button>
                                                    </td>
                                                </tr>

                                                <!-- 'Reviewed' Modal -->
                                                <div class="modal fade" id="confirmReviewModal{{ $report->id }}"
                                                    tabindex="-1" aria-labelledby="confirmReviewModalLabel"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="confirmReviewModalLabel">Confirm
                                                                    Review</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Are you sure you want to mark this report as "Reviewed" and
                                                                forward it to the approver?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Cancel</button>
                                                                <form
                                                                    action="{{ route('reviewer.reports.reviewed', $report->id) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button type="submit"
                                                                        class="btn btn-success">Confirm</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- 'Need Changes' Modal -->
                                                <div class="modal fade" id="needChangesModal{{ $report->id }}"
                                                    tabindex="-1" aria-labelledby="needChangesModalLabel"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="needChangesModalLabel">Need
                                                                    Changes Feedback</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('reviewer.reports.needchanges') }}"
                                                                method="POST">
                                                                @csrf
                                                                <input type="hidden" name="report_id"
                                                                    value="{{ $report->id }}">
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label for="feedback" class="form-label">Feedback
                                                                            (Required)
                                                                            :</label>
                                                                        <textarea name="feedback" id="feedback" class="form-control" rows="4" required></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit"
                                                                        class="btn btn-primary">Submit</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- 'Reject' Modal -->
                                                <div class="modal fade" id="rejectModal{{ $report->id }}"
                                                    tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="rejectModalLabel">Reject
                                                                    Report</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('reviewer.reports.reject') }}"
                                                                method="POST">
                                                                @csrf
                                                                <input type="hidden" name="report_id"
                                                                    value="{{ $report->id }}">
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label for="feedback " class="form-label">Feedback
                                                                            (Optional):</label>
                                                                        <textarea name="feedback" id="feedback" class="form-control" rows="4"></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-danger">Reject
                                                                        Report</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </tbody>
                                    </table>
                            </div>
                            <div class="container">
                                <!-- Custom Pagination Links -->
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination justify-content-center">
                                        <!-- Previous Button -->
                                        <li class="page-item {{ $reports->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $reports->previousPageUrl() }}"
                                                tabindex="-1">Previous</a>
                                        </li>

                                        <!-- Page Number Links -->
                                        @php
                                            $currentPage = $reports->currentPage(); // Current page number
                                            $lastPage = $reports->lastPage(); // Last page number
                                            $start = max($currentPage - 1, 1); // Calculate start of the visible page items
                                            $end = min($start + 2, $lastPage); // Calculate end of the visible page items
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
        </div>
    </div>
    </div> <!--end::Container-->
    </div>
    <!--end::App Content-->
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.select2-multiple').select2({
                width: '100%',
                placeholder: 'Select SDGs',
                allowClear: true
            });
        });
    </script>
@endsection
