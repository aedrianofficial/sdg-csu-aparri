@extends('layouts.publisher')
@section('title', 'Publishing for Report Lists')
@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Publishing for Reports</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Publishing for Reports
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('publisher.reports.index') }}" method="GET" class="mb-4">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="search" class="form-label">Search:</label>
                                        <input id="search" type="text" name="title" class="form-control"
                                            value="{{ request('title') }}" placeholder="Enter Title">
                                    </div>
                                </div>
                                <h6>Filter by:</h6>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="project_status" class="form-label">Related Type:</label>
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

                                    <div class="col-md-3 mb-3">
                                        <label for="sdg" class="form-label">Select SDG:</label>
                                        <select id="select2-multiple" name="sdg[]" class="form-select select2-multiple"
                                            multiple>
                                            @foreach ($sdgs as $sdg)
                                                <option value="{{ $sdg->id }}"
                                                    {{ in_array($sdg->id, request('sdg', [])) ? 'selected' : '' }}>
                                                    {{ $sdg->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-start mb-3">
                                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                                    <a href="{{ route('publisher.reports.index') }}" class="btn btn-secondary">Reset</a>
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
                                                        <a href="{{ route('publisher.reports.show', $report->id) }}"
                                                            class="btn btn-sm btn-success">View</a>
                                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                            data-bs-target="#publishModal"
                                                            data-id="{{ $report->id }}">Publish</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <h3 class="text-danger text-center">No reports found</h3>
                                @endif
                            </div>

                            <!-- Publish Modal -->
                            <div class="modal fade" id="publishModal" tabindex="-1" aria-labelledby="publishModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="publishModalLabel">Publish Report</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to publish this report?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <form id="publishForm"
                                                action="{{ route('publisher.reports.published', ':id') }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Publish</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="container">
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination justify-content-center">
                                        <li class="page-item {{ $reports->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $reports->previousPageUrl() }}"
                                                tabindex="-1">Previous</a>
                                        </li>
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
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#select2-multiple').select2({
                width: '100%',
                placeholder: 'Select SDGs',
                allowClear: true
            });

            // Handle the publish button click event
            $('#publishModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var reportId = button.data('id'); // Extract info from data-* attributes
                var actionUrl = $('#publishForm').attr('action').replace(':id',
                    reportId); // Update form action
                $('#publishForm').attr('action', actionUrl);
            });
        });
    </script>
@endsection