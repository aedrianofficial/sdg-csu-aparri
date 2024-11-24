@extends('layouts.approver')
@section('title', 'Approval for Report Lists')
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Approval for Reports</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Approval for Reports
                        </li>
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
                            <form action="{{ route('approver.reports.index') }}" method="GET" class="mb-4">
                                <div class="row">
                                    <!-- Title Filter -->
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <h6 for="title">Search: </h6>
                                            <input id="search" type="text" name="title" class="form-control"
                                                value="{{ request('title') }}" placeholder="Enter Title">
                                        </div>
                                    </div>
                                </div>

                                <h6>Filter by:</h6>

                                <div class="row">
                                    <!-- Related Type Filter -->
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="related_type" class="form-label">Related Type (Project/Research):
                                            </label>
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
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="sdg" class="form-label">Select SDG: </label>
                                            <select id="select2-multiple" name="sdg[]"
                                                class="form-select select2-multiple" multiple>
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
                                <div class="d-flex justify-content-start">
                                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                                    <a href="{{ route('approver.reports.index') }}" class="btn btn-secondary">Reset</a>
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
                                                                <li>{{ Str::limit($sdg->name, 15, '... ') }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </td>
                                                    <td>{{ $report->is_publish == 1 ? 'Published' : 'Draft' }}</td>
                                                    <td>{{ $report->reviewStatus ? Str::limit($report->reviewStatus->status, 15, '...') : 'N/A' }}
                                                    </td>
                                                    <td>{{ ucfirst($report->related_type) }}</td>
                                                    <td>{{ Str::limit($report->related_title, 15, '...') }}</td>
                                                    <td>
                                                        <a href="{{ route('approver.reports.show', $report->id) }}"
                                                            class="btn btn-sm btn-success">View</a>
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#rejectModal{{ $report->id }}">Reject</button>
                                                        <button type="button" class="btn btn-sm btn-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#approveModal{{ $report->id }}">Approve</button>

                                                        <!-- 'Reject' Modal -->
                                                        <div class="modal fade" id="rejectModal{{ $report->id }}"
                                                            tabindex="-1" aria-labelledby="rejectModalLabel"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="rejectModalLabel">Reject
                                                                            Report</h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>
                                                                    <form action="{{ route('approver.reports.reject') }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="report_id"
                                                                            value="{{ $report->id }}">
                                                                        <div class="modal-body">
                                                                            <div class="mb-3">
                                                                                <label for="feedback"
                                                                                    class="form-label">Feedback
                                                                                    (Optional):</label>
                                                                                <textarea name="feedback" id="feedback" class="form-control" rows="4"></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary"
                                                                                data-bs-dismiss="modal">Close</button>
                                                                            <button type="submit"
                                                                                class="btn btn-danger">Reject
                                                                                Report</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- 'Approve' Modal -->
                                                        <div class="modal fade" id="approveModal{{ $report->id }}"
                                                            tabindex="-1" aria-labelledby="approveModalLabel"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="approveModalLabel">
                                                                            Approve Report</h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        Are you sure you want to approve this report?
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-bs-dismiss="modal">Close</button>
                                                                        <form
                                                                            action="{{ route('approver.reports.approved', $report->id) }}"
                                                                            method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('PUT')
                                                                            <button type="submit"
                                                                                class="btn btn-success">Confirm
                                                                                Approval</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <h3 class="text-danger text-center">No reports found</h3>
                                @endif
                            </div>

                            <!-- Custom Pagination Links -->
                            <div class="d-flex justify-content-center mt-3">
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination">
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
                                            <li class="page-item {{ $currentPage == $i ? ' active' : '' }}">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!--end::Container-->
    </div> <!--end::App Content-->
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
